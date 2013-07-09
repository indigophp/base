<?php

namespace Auth;

class Controller_Auth extends \Controller_Theme
{

	public $template = "layout";

	public function before($data = null)
	{
		if (condition)
		{
			# code...
		}
	}

	public function action_index() {
		return \Response::redirect('auth/login');
	}

	public function action_login()
	{
		$this->template->title = __('auth.login.title');
		$this->theme->set_partial('content', 'login');
	}

	public function action_logout($url = null)
	{
		$this->template->title = __('auth.logout.title');
		$this->theme->set_partial('content', 'logout');
	}

	public function action_register()
	{
		$this->template->title = __('auth.register.title');
		$this->theme->set_partial('content', 'register');
	}

	public function action_reset()
	{
		$this->template->title = __('auth.reset.title');
		$this->theme->set_partial('content', 'reset');
	}

	public function action_confirm()
	{
		$this->template->title = __('auth.confirm.title');
		$this->theme->set_partial('content', 'confirm');
	}

}
