<?php

namespace Auth;

class Controller_Admin extends \Admin\Controller_Admin
{

	public function before($data = null)
	{
		parent::before($data);
	}

	public function action_index()
	{
		$this->template->content = $this->theme->view('list');
	}

	public function action_create($clone_id = null)
	{
		if (!Auth::has_access('users.create'))
		{
			\Session::set_flash('error', 'You are not authorized to create users.');
			\Response::redirect_back();
		}
		$this->template->content = $this->theme->view('user/create.twig');
		$this->template->content->groups = Model\Auth_Group::query()->get();
		$this->template->content->default_group = 3;
		if (\Input::method() == 'GET')
		{
			$this->template->content->model = Model\Auth_User::query()->where('id', $clone_id)->get_one();
			return;
		}
		unset($_POST['password2']);
		unset($_POST['submit']);
		unset($_POST['save']);
		try {
			if (\Auth::create_user(
				\Input::post('username'),
				\Input::post('password'),
				\Input::post('email'),
				\Input::post('group'),
				\Input::post()
			) === false)
			{
				\Session::set_flash('error', 'Couldn\'t create user.');
				$this->template->content->model = Model\Auth_User::forge(\Input::post());
			}
			else
			{
				\Session::set_flash('success', 'User successfully created.');
				\Response::redirect('admin/auth');
			}
		}
		catch (\SimpleUserUpdateException $e)
		{
			\Session::set_flash('error', $e->getMessage());
			$this->template->content->model = Model\Auth_User::forge(\Input::post());
		}
		
	}

	public function action_delete($id = null)
	{
		if (is_null($id))
		{
			throw new HttpNotFoundException();
		}

		if (!Auth::has_access('users.delete'))
		{
			throw new HttpForbiddenException();
		}

		$model = Model\Auth_User::query()->where('id', $id)->get_one();
		if (!$model)
		{
			throw new HttpNotFoundException();
		}

		if (Auth::delete_user($model->username))
		{
			\Response::redirect_back();
		}

		throw new HttpNotFoundException();

	}

}
