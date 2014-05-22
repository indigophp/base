<?php

namespace Admin;

use Orm\Model;
use Orm\Query;
use Fuel\Validation\Validator;
use Fuel\Fieldset\Form;
use Fuel\Common\Arr;
use League\Fractal;

abstract class Controller_Admin_Skeleton extends Controller_Admin
{
	/**
	 * Module name
	 *
	 * @var string
	 */
	protected $module;

	/**
	 * Parsed url of module
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Parsed model name
	 *
	 * @var string
	 */
	protected $model;

	public function before($data = null)
	{
		parent::before($data);

		$this->access();

		if (empty($this->url))
		{
			$this->url = \Uri::admin() . str_replace('_', '/', $this->module);
		}

		\View::set_global('module', $this->module);
		\View::set_global('item', $this->name());
		\View::set_global('items', $this->name(999));
		\View::set_global('url', $this->url);
	}

	/**
	 * Return a translated name based on count
	 *
	 * @param  integer $count
	 * @return string
	 */
	public function name($count = 1)
	{
		return ngettext($this->name[0], $this->name[1], $count);
	}

	/**
	 * Overrideable method to create new view
	 *
	 * @param   string  $view         View name
	 * @param   array   $data         View data
	 * @param   bool    $auto_filter  Auto filter the view data
	 * @return  View    New View object
	 */
	public function view($view, $data = array(), $auto_filter = null)
	{
		return $this->theme->view($view, $data, $auto_filter);
	}

	/**
	 * Check whether user has acces to view page
	 */
	protected function access($access = null)
	{
		if ( ! $this->has_access($this->request->action))
		{
			\Session::set_flash(
				'error',
				\Str::trans(
					gettext('You are not authorized to %action% %items%.'),
					array(
						'%action%' => $this->request->action,
						'%items%'  => $this->name(999),
					)
				)
			);

			return \Response::redirect_back(\Uri::admin(false));
		}
	}

	/**
	 * Check whether user has access to something
	 *
	 * @param  string  $access Resource
	 * @return boolean
	 */
	public function has_access($access)
	{
		return \Auth::has_access($this->module . '.' . $access);
	}

	/**
	 * Creates a new query
	 *
	 * @param   array
	 * @return  Query
	 */
	protected function query($options = array())
	{
		return call_user_func(array($this->model, 'query'), $options);
	}

	/**
	 * Finds an entity of model
	 *
	 * @param   int
	 * @return  Model
	 */
	protected function find($id = null)
	{
		$query = $this->query();
		$query->where('id',  $id)->rows_limit(1);

		if (is_null($id) or is_null($model = $query->get_one()))
		{
			throw new \HttpNotFoundException();
		}

		return $model;
	}

	/**
	 * Forge a new Model
	 *
	 * @param  array   $data
	 * @param  boolean $new
	 * @param  [type]  $view
	 * @param  boolean $cache
	 * @return Model
	 */
	protected function forge($data = array(), $new = true, $view = null, $cache = true)
	{
		return call_user_func(array($this->model, 'forge'), $data, $new, $view, $cache);
	}

	/**
	 * Create new Form instance
	 *
	 * @return Form
	 */
	public function form()
	{
		return call_user_func(array($this->model, 'forgeForm'));
	}

	/**
	 * Create new Validator instance
	 *
	 * @return Validator
	 */
	public function validation()
	{
		return call_user_func(array($this->model, 'forgeValidator'));
	}

	/**
	 * Create new Transformer instance
	 *
	 * @param  boolean $actions Use actions
	 * @return SkeletonTransformer
	 */
	public function transformer($actions = true)
	{
		return new Fractal\Transformer\SkeletonTransformer($this, $actions);
	}

	/**
	 * Get filters for list
	 *
	 * @return array
	 */
	public function filters()
	{
		return call_user_func(array($this->model, 'generateFilters'));
	}

	/**
	 * Redirect page
	 *
	 * @param  string  $url
	 * @param  string  $method
	 * @param  integer $code
	 */
	protected function redirect($url = '', $method = 'location', $code = 302)
	{
		return \Response::redirect($url, $method, $code);
	}

	/**
	 * Decide whether the call is ajax or not
	 * Helps in development
	 *
	 * @return boolean
	 */
	protected function is_ajax()
	{
		if (\Fuel::$env == \Fuel::DEVELOPMENT)
		{
			return \Input::extension();
		}

		return \Input::is_ajax();
	}

	/**
	 * Process query for ajax request
	 *
	 * @param  Query $query    Query object
	 * @param  array $columns  Column definitions
	 * @param  array $defaults Default column values
	 * @return int Items count
	 */
	protected function process_query(Query $query, array $columns = array(), array $defaults = array())
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

			if ($eav = \Arr::get($value, 'eav', false))
			{
				$query->related($rel . '.' . $eav);
			}

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

		$partial_items_count = $query->count();

		// Limit query
		$query
			->limit(\Input::param('iDisplayLength', 10))
			->offset(\Input::param('iDisplayStart', 0));

		return array($all_items_count, $partial_items_count);
	}

	public function action_index()
	{
		if ($ext = $this->is_ajax())
		{
			$properties = call_user_func(array($this->model, 'lists'));

			$query = $this->query();

			$count = $this->process_query($query, $properties);

			$models = $query->get();

			$resource = new Fractal\Resource\Collection($models, $this->transformer());
			$manager = new Fractal\Manager;

			$models = $manager->createData($resource)->toArray();

			$data = array(
				'sEcho' => \Input::param('sEcho'),
				'iTotalRecords' => $count[0],
				'iTotalDisplayRecords' => $count[1],
				'aaData' => $models['data']
			);

			in_array($ext, array('xml', 'json')) or $ext = 'json';

			$data = \Format::forge($data)->{'to_' . $ext}();

			return \Response::forge($data, 200, array('Content-type' => 'application/' . $ext));
		}
		else
		{
			$this->template->set_global('title', ucfirst($this->name(999)));

			$this->template->content = $this->view('admin/skeleton/list');
			$this->template->content->set('filters', $this->filters(), false);
		}
	}

	public function action_create()
	{
		$form = $this->form();

		if (\Input::method() == 'POST')
		{
			$post = \Input::post();

			$validator = $this->validation();
			$result = $validator->run($post);

			if ($result->isValid())
			{
				$model = $this->forge();
				$data = \Arr::filter_keys($post, $result->getValidated());
				$model->set($data)->save();

				\Session::set_flash('success', ucfirst(
					\Str::trans(gettext('%item% successfully created.'), '%item%', $this->name())
				));

				return $this->redirect($this->url() . '/view/' . $model->id);
			}
			else
			{
				$form->repopulate();
				$errors = $result->getErrors();

				\Session::set_flash('error', gettext('There were some errors.'));
			}
		}

		$this->template->set_global('title', ucfirst(
			\Str::trans(gettext('New %item%'), '%item%', $this->name())
		));

		$this->template->content = $this->view('admin/skeleton/create');
		$this->template->content->set('form', $form, false);
		isset($errors) and $this->template->content->set('errors', $errors, false);
	}

	public function action_view($id = null)
	{
		$model = $this->find($id);

		$this->template->set_global('title', ucfirst(
			\Str::trans(gettext('View %item%'), '%item%', $this->name())
		));
		$this->template->content = $this->view('admin/skeleton/view');
		$this->template->content->set('model', $model, false);
	}

	public function action_edit($id = null)
	{
		$model = $this->find($id);
		$form = $this->form();

		if (\Input::method() == 'POST')
		{
			$post = \Input::post();

			$validator = $this->validation();
			$result = $validator->run($post);

			if ($result->isValid())
			{
				$data = \Arr::filter_keys($post, $result->getValidated());
				$model->set($data)->save();

				\Session::set_flash('success', ucfirst(
					\Str::trans(gettext('%item% successfully updated.'), '%item%', $this->name())
				));

				return $this->redirect($this->url);
			}
			else
			{
				$form->repopulate();
				$errors = $result->getErrors();

				\Session::set_flash('error', gettext('There were some errors.'));
			}
		}
		else
		{
			$form->populate($model);
		}

		$this->template->set_global('title', ucfirst(
			\Str::trans(gettext('Edit %item%'), '%item%', $this->name())
		));

		$this->template->content = $this->view('admin/skeleton/edit');
		$this->template->content->set('model', $model, false);
		$this->template->content->set('form', $form, false);
		isset($errors) and $this->template->content->set('errors', $errors, false);
	}

	public function action_delete($id = null)
	{
		$model = $this->find($id);

		if ($model->delete())
		{
			$message = \Str::trans(gettext('%item% successfully deleted.'), '%item%', $this->name());
		}
		else
		{
			$message = \Str::trans(gettext('%item% cannot be deleted.'), '%item%', $this->name());
		}

		\Session::set_flash('success', ucfirst($message));

		return \Response::redirect_back();
	}
}
