<?php

class Uri extends \Fuel\Core\Uri
{
	/**
	 * Gets the admin URL.
	 *
	 * @return  string
	 */
	public static function admin()
	{
		return \Config::get('admin_url');
	}
}
