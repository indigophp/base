<?php

namespace Admin;

class Controller_Enum extends \Admin\Controller_Admin_Skeleton
{
	public static function _init()
	{
		static::$translate = array(
			'create' => array(
				'access' => gettext('You are not authorized to paste wisecracks.')
			),
			'details' => array(
				'access' => gettext('You are not authorized to view wisecracks.')
			),
			'edit' => array(
				'access' => gettext('You are not authorized to edit wisecracks.')
			),
			'delete' => array(
				'access' => gettext('You are not authorized to delete wisecracks.')
			)
		);

	}

	public function query($options = array())
	{
		$query = parent::query();

		if ( ! \Auth::has_access('enum.all'))
		{
			$query->where('read_only', 0);
		}

		return $query;
	}

	protected function model()
	{
		return 'Model_Enum';
	}

	protected function view($view, $data = array(), $auto_filter = null)
	{
		switch ($this->request->action)
		{
			case 'view':
				$view = 'admin/enum/view';
				break;
			default:
				break;
		}

		return parent::view($view, $data, $auto_filter);
	}

	protected function map(\Orm\Model $model, array $properties)
	{
		$data = parent::map($model, $properties);

		empty($data[1]) and $data[1] = gettext('<i>None</i>');
		$data[2] = $data[2] ? gettext('Yes') : gettext('No');
		$data[3] = $data[3] ? gettext('Yes') : gettext('No');

		return $data;
	}

	// public function action_create($clone_id = null)
	// {
	// 	if ( ! \Auth::has_access('enum.create'))
	// 	{
	// 		\Session::set_flash('error', gettext('You are not authorized to create enum.'));
	// 		\Response::redirect_back('admin/enums');
	// 	}

	// 	$this->template->content = $this->theme->view('admin/enums/create');
	// }

	// public function post_create()
	// {
	// 	if ( ! \Auth::has_access('enum.create'))
	// 	{
	// 		\Session::set_flash('error', gettext('You are not authorized to create enum.'));
	// 		\Response::redirect_back('admin/enums');
	// 	}

	// 	$val = \Validation::forge('enums');

	// 	$val->add_field('name', gettext('Name'), 'required|trim');
	// 	$val->add('description', gettext('Description'));
	// 	$val->add('default_id', gettext('Default'));
	// 	$val->add_field('active', gettext('Active'), 'required|numeric_min[-1]|numeric_max[2]');

	// 	if (\Auth::has_access('enum.all'))
	// 	{
	// 		$val->add_field('read_only', gettext('Read-only'), 'required|numeric_min[-1]|numeric_max[2]');
	// 	}

	// 	if ($val->run() === true)
	// 	{
	// 		\Model_Enum::forge($val->validated())->save();
	// 		\Session::set_flash('success', gettext('Enum successfully created.'));
	// 		\Response::redirect('admin/enums');
	// 	}
	// 	else
	// 	{
	// 		$this->template->content = $this->theme->view('admin/enums/create');
	// 		$this->template->content->model = $val->input();
	// 		$this->template->content->val = $val;
	// 		\Session::set_flash('error', gettext('There were some errors.'));
	// 	}
	// }

	// public function action_details($id = null)
	// {
	// 	$model = $this->model();

	// 	$model = $model::query()
	// 		->related('items')
	// 		->related('items.meta')
	// 		->where('id', $id)
	// 		->order_by('items.sort');

	// 	if ( ! \Auth::has_access('enum.all'))
	// 	{
	// 		$model->where('read_only', 0);
	// 	}

	// 	if (is_null($id) or is_null($model = $model->get_one()))
	// 	{
	// 		throw new \HttpNotFoundException();
	// 	}

	// 	$this->template->content = $this->theme->view('admin/enums/details.twig');
	// 	$this->template->content->set('model', $model, false);
	// }

	// protected function getEnum($id = null)
	// {
	// 	if (is_null($id))
	// 	{
	// 		throw new \HttpNotFoundException();
	// 	}

	// 	$model = \Model_Enum::query()->where('id', $id);

	// 	if ( ! \Auth::has_access('enum.all'))
	// 	{
	// 		$model->where('read_only', 0);
	// 	}

	// 	$model = $model->get_one();

	// 	if ( ! $model)
	// 	{
	// 		throw new \HttpNotFoundException();
	// 	}

	// 	return $model;
	// }

	// public function action_edit($id = null)
	// {
	// 	if ( ! \Auth::has_access('enum.edit'))
	// 	{
	// 		\Session::set_flash('error', gettext('You are not authorized to edit enum.'));
	// 		\Response::redirect_back();
	// 	}

	// 	$model = $this->getEnum($id);

	// 	$this->template->content = $this->theme->view('admin/enums/edit');
	// 	$this->template->content->set('model', $model, false);
	// }

	// public function post_edit($id = null)
	// {
	// 	if ( ! \Auth::has_access('enum.edit'))
	// 	{
	// 		\Session::set_flash('error', gettext('You are not authorized to edit enum.'));
	// 		\Response::redirect_back();
	// 	}

	// 	$model = $this->getEnum($id);

	// 	$val = \Validation::forge('enums');

	// 	$val->add_field('name', gettext('Name'), 'required|trim');
	// 	$val->add('description', gettext('Description'));
	// 	$val->add('default_id', gettext('Default'));
	// 	$val->add_field('active', gettext('Active'), 'required|numeric_min[-1]|numeric_max[2]');
	// 	$val->add_field('read_only', gettext('Read-only'), 'required|numeric_min[-1]|numeric_max[2]');

	// 	if ($val->run() === true)
	// 	{
	// 		$model->set($val->validated())->save();
	// 		\Session::set_flash('success', gettext('Enum successfully updated.'));
	// 		\Response::redirect('admin/enums/details/' . $id);
	// 	}
	// 	else
	// 	{
	// 		$this->template->content = $this->theme->view('admin/enums/edit');
	// 		$this->template->content->model = $val->input();
	// 		$this->template->content->val = $val;
	// 		\Session::set_flash('error', gettext('There were some errors.'));
	// 	}
	// }

	// public function action_delete($id = null)
	// {
	// 	if ( ! \Auth::has_access('enum.delete'))
	// 	{
	// 		\Session::set_flash('error', gettext('You are not authorized to delete enum.'));
	// 		\Response::redirect_back();
	// 	}

	// 	$model = $this->getEnum($id);

	// 	if ($model->delete())
	// 	{
	// 		\Session::set_flash('success', gettext('Enum successfully deleted.'));
	// 		\Response::redirect('admin/enums');
	// 	}
	// 	else
	// 	{
	// 		\Session::set_flash('error', gettext('Could not delete enum.'));
	// 		\Response::redirect_back();
	// 	}

	// }

	public function action_create_item($enum_id = null)
	{
		if ( ! \Auth::has_access('enum.create_item'))
		{
			\Session::set_flash('error', gettext('You are not authorized to create enum items.'));
			\Response::redirect_back('admin/enums');
		}

		$enum = $this->find($enum_id);

		$this->template->content = $this->theme->view('admin/enums/create_item');
		$this->template->content->set('enum', $enum, false);
	}

	public function post_create_item($enum_id = null)
	{
		if ( ! \Auth::has_access('enum.create_item'))
		{
			\Session::set_flash('error', gettext('You are not authorized to create enum items.'));
			\Response::redirect_back('admin/enums');
		}

		$enum = $this->getEnum($enum_id);

		$val = \Validation::forge('enums');

		$val->add_field('name', gettext('Name'), 'required|trim');
		$val->add('description', gettext('Description'));
		$val->add_field('active', gettext('Active'), 'required|numeric_min[-1]|numeric_max[2]');

		if ($val->run() === true)
		{
			\Model_Enum_Item::forge($val->validated())->set('enum', $enum)->save();
			\Session::set_flash('success', gettext('Enum item successfully added.'));
			\Response::redirect('admin/enums/details/' . $enum_id);
		}
		else
		{
			$this->template->content = $this->theme->view('admin/enums/create_item');
			$this->template->content->set('enum', $enum, false);
			$this->template->content->model = $val->input();
			$this->template->content->val = $val;
			\Session::set_flash('error', gettext('There were some errors.'));
		}
	}

	protected function getEnumItem($id = null)
	{
		if (is_null($id))
		{
			throw new \HttpNotFoundException();
		}

		$model = \Model_Enum_Item::query()->related('enum')->where('id', $id);

		if ( ! \Auth::has_access('enum.all'))
		{
			$model->where('enum.read_only', 0);
		}

		$model = $model->get_one();

		if ( ! $model or ! $model->enum)
		{
			throw new \HttpNotFoundException();
		}

		return $model;
	}

	public function action_edit_item($id = null)
	{
		if ( ! \Auth::has_access('enum.edit_item'))
		{
			\Session::set_flash('error', gettext('You are not authorized to edit enum items.'));
			\Response::redirect_back();
		}

		$model = $this->getEnumItem($id);

		$this->template->content = $this->theme->view('admin/enums/edit_item');
		$this->template->content->set('model', $model, false);
		$this->template->content->set('enum', $model->enum, false);
	}

	public function post_edit_item($id = null)
	{
		if ( ! \Auth::has_access('enum.edit'))
		{
			\Session::set_flash('error', gettext('You are not authorized to edit enum items.'));
			\Response::redirect_back();
		}

		$model = $this->getEnumItem($id);

		$val = \Validation::forge('enums');

		$val->add_field('name', gettext('Name'), 'required|trim');
		$val->add('description', gettext('Description'));
		$val->add('default_id', gettext('Default'));
		$val->add_field('active', gettext('Active'), 'required|numeric_min[-1]|numeric_max[2]');

		if ($val->run() === true)
		{
			$model->set($val->validated())->save();
			\Session::set_flash('success', gettext('Enum item successfully updated.'));
			\Response::redirect('admin/enums/details/' . $model->enum->id);
		}
		else
		{
			$this->template->content = $this->theme->view('admin/enums/edit_item');
			$this->template->content->model = $val->input();
			$this->template->content->set('enum', $model->enum, false);
			$this->template->content->val = $val;
			\Session::set_flash('error', gettext('There were some errors.'));
		}
	}

	public function action_delete_item($id = null)
	{
		if ( ! \Auth::has_access('enum.delete_item'))
		{
			\Session::set_flash('error', gettext('You are not authorized to delete enum items.'));
			\Response::redirect_back();
		}

		$model = $this->getEnumItem($id);

		$enum_id = $model->enum->id;

		if ($model->delete())
		{
			\Session::set_flash('success', gettext('Enum item successfully deleted.'));
			\Response::redirect('admin/enums/details/' . $enum_id);
		}
		else
		{
			\Session::set_flash('error', gettext('Could not delete enum item.'));
			\Response::redirect_back();
		}

	}
}
