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
		$this->template->content = $this->theme->view('user/create.twig');
		$this->template->content->groups = Model\Auth_Group::query()->get();
		$this->template->content->default_group = 3;
	}

}
