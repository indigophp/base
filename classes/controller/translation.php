<?php

class Controller_Translation extends Controller_Rest
{

	public function action_datatables()
	{
		return Config::load('datatables/'.Config::get('language'));
	}

}