<?php

namespace Admin;

abstract class Controller_Admin_Skeleton extends Controller_Admin
{
	public function before($data = null)
	{
		parent::before($data);

		$translate = $this->translate();

		if ( ! \Auth::has_access($this->request->module . '.' . $this->request->action))
		{
			\Session::set_flash('error', \Arr::get($translate, $this->request->action . '.access', gettext('You are not authorized to do this.')));
			return \Response::redirect_back('admin/' . $this->request->module);
		}
	}

	protected function validation($instance = null)
	{
		return \Validation::forge(is_null($instance) ? $this->request->module : $instance);
	}

	protected function translate()
	{
		return array();
	}

	protected function find($id = null, $model = null)
	{
		is_null($model) and $model = '\\' . ucfirst($this->request->module) . '\\' .'Model_' . ucfirst($this->request->module);

		if (is_null($id) or is_null($model = $model::find($id)))
		{
			throw new \HttpNotFoundException();
		}

		return $model;
	}
}