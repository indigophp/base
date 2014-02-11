<?php

namespace Admin;

abstract class Controller_Admin_Config extends Controller_Admin
{
	/**
	 * Parsed module name
	 *
	 * @var string
	 */
	protected $_module;

	/**
	 * Name of the config
	 *
	 * @var string
	 */
	protected $_config;

	protected $_group = true;

	protected $_save;

	protected $_config_cached;

	protected $_properties = array();

	protected $_fieldsets = array(
		'settings' => array(
			'label' => 'Beállítások',
			'properties' => true,
		),
	);


	protected static $translate = array();

	public function before($data = null)
	{
		parent::before($data);

		$translate = $this->translate();
		// $this->access();

		\View::set_global('module', $this->module());
		// \View::set_global('module_name', $this->name());
		// \View::set_global('url', $this->url());
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

	/**
	 * Check whether user has acces to view page
	 */
	protected function access($access = null)
	{
		if ( ! $this->has_access($this->request->action))
		{
			\Session::set_flash('error', \Arr::get(static::$translate, $this->request->action . '.access', gettext('You are not authorized to do this.')));
			return \Response::redirect_back(\Uri::admin(false));
		}
	}

	/**
	 * Check whether user has access to something
	 *
	 * @param  string  $access Resource
	 * @return boolean
	 */
	protected function has_access($access)
	{
		return \Auth::has_access($this->module() . '.' . $access);
	}

	/**
	 * Return validation object
	 *
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

	protected function load()
	{
		if (isset($this->_config_cached))
		{
			return $this->_config_cached;
		}

		is_array($this->_config) or $this->_config = array($this->_config => $this->_group);
		$this->_config_cached = array();

		foreach ($this->_config as $_config => $_group)
		{
			if (is_int($_config))
			{
				$_config = $_group;
				$_group = $this->_group;
			}

			if (false === $config = \Config::load($_config, $_group))
			{
				$config = \Config::get($_group, array());
			}

			$this->_config_cached = \Arr::merge($this->_config_cached, $config);
		}

		return $this->_config_cached;
	}

	public function fieldsets(array $config)
	{
		$fieldsets = $this->_fieldsets;

		foreach ($fieldsets as $key => $value)
		{
			$properties = \Arr::get($value, 'properties', array());

			if ($properties === true)
			{
				$global = $key;
				continue;
			}

			$fieldsets[$key]['properties'] = \Arr::filter_keys($config, $properties, false);
			$config = \Arr::filter_keys($config, $properties, true);
		}

		if ( ! isset($global))
		{
			throw new \InvalidArgumentException('There should be one global fieldset');
		}

		$fieldsets[$global]['properties'] = $config;

		return $fieldsets;
	}

	public function save(array $config, $file = null)
	{
		is_null($file) and $file = $this->_save;
		is_null($file) and $file = end($this->_config);

		\Config::save($file, $config);
	}

	public function action_index()
	{
		$config = $this->load();
		$config = \Arr::flatten_assoc($config, '.');

		$fieldsets = $this->fieldsets($config);

		$this->template->set_global('title', 'Valami');
		$this->template->content = $this->view('admin/config/view');
		$this->template->content->set('fieldsets', $fieldsets, false);
		$this->template->content->set('properties', $this->_properties, false);
	}

	public function action_edit($file = null)
	{
		$config = $this->load();
		$config = \Arr::flatten_assoc($config, '.');

		$fieldsets = $this->fieldsets($config);

		$this->template->set_global('title', 'Valami');
		$this->template->content = $this->view('admin/config/edit');
		$this->template->content->set('fieldsets', $fieldsets, false);
		$this->template->content->set('properties', $this->_properties, false);
	}

	public function post_edit($file = null)
	{
		$config = \Input::post();
		$config = \Arr::reverse_flatten($config, '.');
		// $model = $this->find($id);
		// $properties = $model->form();

		// $val = $this->val($properties);

		// if ($val->run() === true)
		// {
		// 	$model->set($val->validated())->save();
		// 	\Session::set_flash('success', ucfirst(strtr(gettext('%item% successfully updated.'), array('%item%' => $this->name()[0]))));
		// 	return $this->redirect($this->url());
		// }
		// else
		// {
		// 	$this->template->set_global('title', ucfirst(strtr(gettext('Edit %item%'), array('%item%' => $this->name()[0]))));
		// 	$this->template->content = $this->view('admin/skeleton/edit');
		// 	$this->template->content->set('model', $model->set($val->input()), false);
		// 	$this->template->content->set('val', $val, false);
		// 	\Session::set_flash('error', gettext('There were some errors.'));
		// }

		// return false;
	}
}
