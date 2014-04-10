<?php

namespace Auth;

use Fuel\Validation\Validator;

class Controller_Auth extends \Controller_Base
{
	public $template = 'frontend/template';

	public $theme_type = 'frontend';

	public function action_index()
	{
		return \Response::redirect('/auth/login');
	}

	public function redirect()
	{
		return \Response::redirect(\Input::get('uri') ? : '/user');
	}

	public function action_login()
	{
		// Already logged in
		\Auth::check() and \Response::redirect(\Input::get('uri') ? : '/user');

		$this->template->title = gettext('Login');
		$this->template->content = $this->theme->view('auth/login', array('val' => ''), false);
	}

	public function post_login()
	{
		// Already logged in
		\Auth::check() and $this->redirect();

		$validator = new Validator;

		$validator->addField(
			\Config::get('auth.username_post_key', 'username'),
			gettext('E-mail, or username')
		)->required();

		$validator->addField(
			\Config::get('auth.password_post_key', 'password'),
			gettext('Password')
		)->required();

		$result = $validator->run(\Input::post());

		if ($result->isValid())
		{
			$auth = \Auth::instance();

			if ($auth->login())
			{
				$current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
				\Session::set_flash('success', \Str::trans(
					gettext('Welcome, %fullname%!'),
					'%fullname%',
					$current_user->fullname
				));

				return $this->redirect();
			}
			else
			{
				\Session::set_flash('error', gettext('Wrong credentials!'));
			}
		}
		else
		{
			\Session::set_flash('errors', $result->getErrors());
		}

		$this->template->title = gettext('Login');
		$this->template->content = $this->theme->view('auth/login', array('val' => $result), false);
	}

	public function action_logout()
	{
		\Auth::logout();
		\Response::redirect('/auth');
	}
}