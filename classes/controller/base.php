<?php

class Controller_Base extends \Controller_Theme
{

	public static function _init()
	{
		echo "naád";
	}

	public function before($data = null)
	{
		$before = parent::before($data);
		return $before;
	}

}