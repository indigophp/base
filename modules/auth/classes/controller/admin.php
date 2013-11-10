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
		if ( ! Auth::has_access('users.list'))
		{
			return HttpForbiddenException();
		}
		$this->template->content = $this->theme->view('admin/user/list');
	}

	public function action_create($clone_id = null)
	{
		if ( ! Auth::has_access('users.create'))
		{
			\Session::set_flash('error', gettext('You are not authorized to create users.'));
			\Response::redirect_back();
		}
		$this->template->content = $this->theme->view('admin/user/create.twig');
		$this->template->content->groups = Model\Auth_Group::query()->get();
		$this->template->content->default_group = 3;
		if (\Input::method() == 'GET')
		{
			$this->template->content->model = Model\Auth_User::query()->where('id', $clone_id)->get_one();
			return;
		}

		$original_post = \Input::post();

		$username = \Input::post('username');
		$password = \Input::post('password');
		$email    = \Input::post('email');
		$group    = \Input::post('group');

		unset($_POST['password']);
		unset($_POST['password2']);
		unset($_POST['username']);
		unset($_POST['email']);
		unset($_POST['group']);
		unset($_POST['submit']);
		unset($_POST['save']);
		try {
			if (\Auth::create_user(
				$username,
				$password,
				$email,
				$group,
				\Input::post()
			) === false)
			{
				\Session::set_flash('error', gettext('Could not create user.'));
				$this->template->content->model = Model\Auth_User::forge($original_post);
			}
			else
			{
				\Session::set_flash('success', gettext('User successfully created.'));
				\Response::redirect('admin/auth');
			}
		}
		catch (\SimpleUserUpdateException $e)
		{
			\Session::set_flash('error', $e->getMessage());
			$this->template->content->model = Model\Auth_User::forge($original_post);
		}

	}

	public function action_delete($id = null)
	{
		if ( ! Auth::has_access('users.delete'))
		{
			\Session::set_flash('error', gettext('You are not authorized to delete users.'));
			\Response::redirect_back();
		}

		if (is_null($id))
		{
			throw new \HttpNotFoundException();
		}

		$model = Model\Auth_User::query()->where('id', $id)->get_one();
		if ( ! $model)
		{
			throw new \HttpNotFoundException();
		}

		if (Auth::delete_user($model->username))
		{
			\Session::set_flash('success', gettext('Successfully deleted user.'));
		}
		else
		{
			\Session::set_flash('error', gettext('Could not delete user.'));
		}
		\Response::redirect_back();
	}

	public function action_edit($id = null)
	{
		if (is_null($id))
		{
			throw new \HttpNotFoundException();
		}

		$model = Model\Auth_User::query()->where('id', $id)->get_one();
		if (!$model)
		{
			throw new \HttpNotFoundException();
		}

		if (!Auth::has_access('users.edit_other'))
		{
			if (!Auth::has_access('users.edit_own') or Auth::get_screen_name() != $model->username)
			{
				\Session::set_flash('error', gettext('You are not authorized to edit this user.'));
				\Response::redirect_back();
			}
		}

		if (\Input::method() == 'POST')
		{
			// var_dump(\Input::post());
			$input = array_filter(\Input::post());
			// var_dump($input);exit;
			try {
				if (Auth::update_user($input, $model->username))
				{
					\Session::set_flash('success', gettext('User profile saved'));
				}
				else
				{
					\Session::set_flash('error', gettext('Could not save user'));
				}
			} catch (\SimpleUserWrongPassword $e) {
				\Session::set_flash('error', $e->getMessage());
			} catch (\SimpleUserUpdateException $e) {
				\Session::set_flash('error', $e->getMessage());
			}

			\Response::redirect('admin/auth');
		}

		$this->template->content = $this->theme->view('admin/user/edit.twig');
		$this->template->content->groups = Model\Auth_Group::query()->get();
		$this->template->content->default_group = 3;
		$this->template->content->model = $model;
	}

	public function action_details($id = null)
	{
		if (!Auth::has_access('users.view_details'))
		{
			\Session::set_flash('error', gettext('You are not authorized to view users\' details.'));
			\Response::redirect_back();
		}

		if (is_null($id))
		{
			throw new \HttpNotFoundException();
		}

		$model = Model\Auth_User::query()->where('id', $id)->get_one();
		if (!$model)
		{
			throw new \HttpNotFoundException();
		}

		$this->template->content = $this->theme->view('admin/user/details.twig');
		$this->template->content->set('model', $model, false);
	}

}
