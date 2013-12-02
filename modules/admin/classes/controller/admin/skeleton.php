<?php

namespace Admin;

abstract class Controller_Admin_Skeleton extends Controller_Admin
{
	/**
	 * Parsed module name
	 *
	 * @var string
	 */
	protected $_module;

	/**
	 * Parsed url of module
	 *
	 * @var string
	 */
	protected $_url;

	/**
	 * Name of the module
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Parsed model name
	 *
	 * @var string
	 */
	protected $_model;

	protected static $translate = array();

	public function before($data = null)
	{
		parent::before($data);

		$translate = $this->translate();
		$this->access();

		\View::set_global('module', $this->module());
		\View::set_global('module_name', $this->name());
		\View::set_global('url', $this->url());
	}

	/**
	 * Parse module name
	 *
	 * @return string
	 */
	protected function module()
	{
		if ( ! empty($this->_module))
		{
			return $this->_module;
		}

		if ($this->request->module == 'admin')
		{
			$module = \Inflector::denamespace($this->request->controller);
			$module = strtolower(str_replace('Controller_', '', $module));

			return $this->_module = $module;
		}
		else
		{
			return $this->_module = $this->request->module;
		}
	}

	protected function url()
	{
		if ( ! empty($this->_url))
		{
			return $this->_url;
		}

		return $this->_url = \Uri::admin() . str_replace('_', '/', $this->module());
	}

	abstract protected function name();

	/**
	 * Parse model name
	 *
	 * @return string
	 */
	protected function model()
	{
		if ( ! empty($this->_model))
		{
			return $this->_model;
		}

		return $this->_model = '\\' . ucfirst($this->request->module) . '\\' . 'Model_' . \Inflector::classify($this->module());
	}

	/**
	 * Overrideable method to create new view
	 *
	 * @param   string  $view         View name
	 * @param   array   $data         View data
	 * @param   bool    $auto_filter  Auto filter the view data
	 * @return  View    New View object
	 */
	protected function view($view, $data = array(), $auto_filter = null)
	{
		return $this->theme->view($view, $data, $auto_filter);
	}

	protected function translate()
	{
		return array();
	}

	protected function access()
	{
		if ( ! \Auth::has_access($this->module() . '.' . $this->request->action))
		{
			\Session::set_flash('error', \Arr::get(static::$translate, $this->request->action . '.access', gettext('You are not authorized to do this.')));
			return \Response::redirect_back(\Uri::admin(false));
		}
	}

	/**
	 * Creates a new query with optional settings up front
	 *
	 * @param   array
	 * @return  Query
	 */
	protected function query($options = array())
	{
		$model = $this->model();

		return $model::query($options);
	}

	/**
	 * Finds an entity of model
	 *
	 * @param   int
	 * @return  \Orm\Model
	 */
	protected function find($id = null)
	{
		$query = $this->query();
		$query->where('id',  $id);

		if (is_null($id) or is_null($model = $query->get_one()))
		{
			throw new \HttpNotFoundException();
		}

		return $model;
	}

	protected function forge($data = array(), $new = true, $view = null, $cache = true)
	{
		$model = $this->model();
		$model = $model::forge($data, $new, $view, $cache);
		return $model;
	}

	/**
	 * Process query for ajax request
	 *
	 * @param  \Orm\Query $query    Query object
	 * @param  array      $columns  Column definitions
	 * @param  array      $defaults Default column values
	 * @return int                  Items count
	 */
	protected function process_query(\Orm\Query $query, array $columns = array(), array $defaults = array())
	{
		// Count all items
		$all_items_count = $query->count();

		// Process incoming sortng values
		$sort = array();
		for ($i = 0; $i < \Input::param('iSortingCols'); $i++)
		{
			$sort[\Input::param('iSortCol_'.$i)] = \Input::param('sSortDir_'.$i);
		}

		$i = 0;
		$order_by = array();
		$where = array();
		$global_filter = \Input::param('sSearch');

		foreach ($columns as $key => $value)
		{
			$rels = explode('.', $key);

			$rel = '';

			for ($j=0; $j < count($rels) - 1 and count($rels) > 1; $j++)
			{
				if (empty($rel))
				{
					$rel = $rels[$j];
				}
				else
				{
					$rel .= '.' . $rels[$j];
				}

				$query->related($rel);
			}

			$value = \Arr::merge($defaults, $value);

			if (\Input::param('bSortable_'.$i, true) and \Arr::get($value, 'list.sort', true) and array_key_exists($i,  $sort))
			{
				$order_by[$key] = $sort[$i];
			}

			$filter = \Input::param('sSearch_'.$i);

			$filter = json_decode($filter);

			if ( ! in_array($filter, array(null, '', 'null')) and \Input::param('bSearchable_'.$i, true) and \Arr::get($value, 'list.search', true))
			{
				switch (\Arr::get($value, 'list.type', 'text'))
				{
					case 'select-multiple':
					case 'select':
					case 'enum':
						$query->where($key, 'IN', $filter);
						break;
					case 'select-single':
					case 'number':
						$query->where($key, $filter);
						break;
					case 'text':
						$query->where($key, 'LIKE', '%' . $filter . '%');
						break;
					case 'range':
						$query->where($key, 'BETWEEN', $filter);
						break;
					default:
						break;
				}
			}

			if ( ! empty($global_filter))
			{
				if (\Arr::get($value, 'list.search', true) === true and \Arr::get($value, 'list.global', true) === true)
				{
					$where[] = array($key, 'LIKE', '%' . $global_filter . '%');
				}
			}

			$i++;
		}

		if ( ! empty($where))
		{
			$query->where_open();
			foreach ($where as $where)
			{
				$query->or_where($where[0], $where[1], $where[2]);
			}
			$query->where_close();
		}

		// Order query
		$query->order_by($order_by);

		// Limit query
		$query
			->rows_limit(\Input::param('iDisplayLength', 10))
			->rows_offset(\Input::param('iDisplayStart', 0));

		return $all_items_count;
	}

	/**
	 * This function is called in array_map to process the response
	 *
	 * @param  \Orm\Model $model      Returned model instance
	 * @param  array      $properties Properties to use
	 * @return array                  Array of returned elements
	 */
	protected function map(\Orm\Model $model, array $properties)
	{
		$data = $model->to_array();
		$data = \Arr::subset($data, array_keys($properties));
		$data = \Arr::flatten_assoc($data, '.');

		// Check for options and set value
		foreach ($properties as $key => $value)
		{
			if ($options = $model->options($key))
			{
				empty($data) or $data[$key] = $options[$data[$key]];
			}
		}

		$data['action'] =
			'<div class="hidden-print btn-group btn-group-sm" style="width:100px">'.
				(\Auth::has_access($this->module() . '.view') ? '<a href="'.\Uri::create(\Uri::admin() . $this->module() . '/view/' . $model->id).'" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>' : '').
				(\Auth::has_access($this->module() . '.edit') ? '<a href="'.\Uri::create(\Uri::admin() . $this->module() . '/edit/' . $model->id).'" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>' : '').
				(\Auth::has_access($this->module() . '.delete') ? '<a href="'.\Uri::create(\Uri::admin() . $this->module() . '/delete/' . $model->id).'" class="btn btn-default"><span class="glyphicon glyphicon-remove" style="color:#f55;"></span></a>' : '').
			'</div>';
		return array_values($data);
	}

	/**
	 * Return validation object
	 * @param  array $fields
	 * @return \Fuel\Core\Validation
	 */
	protected function val(array $fields = null)
	{
		$val = \Validation::forge($this->module());

		if (empty($fields))
		{
			return $val;
		}

		foreach ($fields as $name => $params)
		{
			if (is_int($name))
			{
				$name = $params;
				$params = array();
			}

			$label = \Arr::get($params, 'label', gettext('Unidentified Property'));

			if ($rules = \Arr::get($params, 'validation'))
			{
				$val->add_field($name, $label, $rules);
			}
			else
			{
				$val->add($name, $label);
			}
		}

		return $val;
	}

	protected function redirect($url = '', $method = 'location', $code = 302)
	{
		return \Response::redirect($url, $method, $code);
	}

	public function action_index()
	{
		$model = $this->model();

		if (\Input::is_ajax())
		{
			$query = $model::query();

			$properties = $model::lists();

			$query = $this->query($query);

			$count = $this->process_query($query, $properties);

			$models = $query->get();

			$data = array(
				'sEcho' => \Input::param('sEcho'),
				'iTotalRecords' => $count,
				'iTotalDisplayRecords' => \DB::count_last_query(),
				'aaData' => array_values(array_map(function($model) use($properties) {
					return $this->map($model, $properties);
				}, $models))
			);

			$ext = \Input::extension();
			in_array($ext, array('xml', 'json')) or $ext = 'json';

			$data = \Format::forge($data)->{'to_' . $ext}();

			return \Response::forge($data, 200, array('Content-type' => 'application/' . $ext));
		}
		else
		{
			$this->template->set_global('title', ucfirst($this->name()[1]));
			$this->template->content = $this->view('admin/skeleton/list');
			$this->template->content->set('model', $this->forge(), false);
		}
	}

	public function action_create()
	{
		$this->template->set_global('title', ucfirst(strtr(gettext('New %item%'), array('%item%' => $this->name()[0]))));
		$this->template->content = $this->view('admin/skeleton/create');
		$this->template->content->set('model', $this->forge(), false);
	}

	public function post_create()
	{
		$model = $this->model();
		$properties = $model::form();
		$model = $this->forge();

		$val = $this->val($properties);

		if ($val->run() === true)
		{
			$model->set($val->validated())->save();
			\Session::set_flash('success', ucfirst(strtr(gettext('%item% successfully created.'), array('%item%' => $this->name()[0]))));
			return $this->redirect($this->url());
		}
		else
		{
			$this->template->set_global('title', ucfirst(strtr(gettext('New %item%'), array('%item%' => $this->name()[0]))));
			$this->template->content = $this->view('admin/skeleton/create');
			$this->template->content->set('model', $model->set($val->input()), false);
			$this->template->content->set('val', $val, false);
			\Session::set_flash('error', gettext('There were some errors.'));
		}

		return false;
	}

	public function action_view($id = null)
	{
		$model = $this->find($id);
		$this->template->set_global('title', ucfirst(strtr(gettext('View %item%'), array('%item%' => $this->name()[0]))));
		$this->template->content = $this->view('admin/skeleton/view');
		$this->template->content->set('model', $model, false);
	}

	public function action_edit($id = null)
	{
		$model = $this->find($id);
		$this->template->set_global('title', ucfirst(strtr(gettext('Edit %item%'), array('%item%' => $this->name()[0]))));
		$this->template->content = $this->view('admin/skeleton/edit');
		$this->template->content->set('model', $model, false);
	}

	public function post_edit($id = null)
	{
		$model = $this->find($id);
		$properties = $model->form();

		$val = $this->val($properties);

		if ($val->run() === true)
		{
			$model->set($val->validated())->save();
			\Session::set_flash('success', ucfirst(strtr(gettext('%item% successfully updated.'), array('%item%' => $this->name()[0]))));
			return $this->redirect($this->url());
		}
		else
		{
			$this->template->set_global('title', ucfirst(strtr(gettext('Edit %item%'), array('%item%' => $this->name()[0]))));
			$this->template->content = $this->view('admin/skeleton/edit');
			$this->template->content->set('model', $model->set($val->input()), false);
			$this->template->content->set('val', $val, false);
			\Session::set_flash('error', gettext('There were some errors.'));
		}

		return false;
	}

	public function action_delete($id = null)
	{
		$model = $this->find($id);

		if ($model->delete())
		{
			\Session::set_flash('success', ucfirst(strtr(gettext('%item% successfully deleted.'), array('%item%' => $this->name()[0]))));
			return $this->redirect($this->url());
		}
		else
		{
			\Session::set_flash('error', ucfirst(strtr(gettext('%item% cannot be deleted.'), array('%item%' => $this->name()[0]))));
			return \Response::redirect_back();
		}
	}
}
