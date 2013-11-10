<?php

class Controller_Welcome extends Controller_Base
{

	public $template = 'frontend/template';

	public function action_index()
	{
		$this->template->content = $this->theme->view('frontend/welcome/index');
	}

	public function action_hello()
	{
		$this->template->content = $this->theme->view('frontend/welcome/hello');
	}

	public function action_404()
	{
		$this->template->content = $this->theme->view('frontend/welcome/404');
	}
}