<?php

namespace Auth;

class Controller_Auth extends \Controller_Theme
{

	public $template = "layout";

	public function action_login()
	{
		$this->template->title = __('auth.login.title');
		$this->theme->set_partial('content', 'login');
	}

	public function action_logout()
	{
		$data["subnav"] = array('logout'=> 'active' );
		$this->template->title = 'Auth &raquo; Logout';
		$this->template->content = View::forge('auth/logout', $data);
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
		$data["subnav"] = array('confirm'=> 'active' );
		$this->template->title = 'Auth &raquo; Confirm';
		$this->template->content = View::forge('auth/confirm', $data);
	}

}
