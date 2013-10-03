<?php

namespace Admin;

class Controller_Admin extends \Controller_Base
{

	public function action_index()
	{
		$this->template->content = $this->theme->view('dashboard');
	}

}
