<?php

namespace Auth;

class Controller_Auth extends \Controller_Theme
{

	public $template = "layout";

	public function before($data = null)
	{
		parent::before($data);

		$this->template->set('title', 'My homepage');
	}

	public function action_login()
	{
		// $data["subnav"] = array('login'=> 'active' );
		// $this->template->title = 'Auth &raquo; Login';
		// $this->template->content = View::forge('auth/login', $data);
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
		$data["subnav"] = array('register'=> 'active' );
		$this->template->title = 'Auth &raquo; Register';
		$this->template->content = View::forge('auth/register', $data);
	}

	public function action_reset()
	{
		$data["subnav"] = array('reset'=> 'active' );
		$this->template->title = 'Auth &raquo; Reset';
		$this->template->content = View::forge('auth/reset', $data);
	}

	public function action_confirm()
	{
		$data["subnav"] = array('confirm'=> 'active' );
		$this->template->title = 'Auth &raquo; Confirm';
		$this->template->content = View::forge('auth/confirm', $data);
	}

}
