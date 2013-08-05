<?php

namespace Admin;

class Controller_Admin extends \Controller
{


	public function action_index()
	{
		$this->theme->set_partial('content', 'login');
	}

}
