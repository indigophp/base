<?php

class Uri extends \Fuel\Core\Uri
{
	/**
	 * Gets the admin URL.
	 *
	 * @return  string
	 */
	public static function admin($absolute = true)
	{
		static $relative_url;
		static $absolute_url;

		if (empty($relative_url))
		{
			$relative_url = \Config::get('admin_url');
			$absolute_url = static::base() . $relative_url;
		}
		return $absolute ? $absolute_url : $relative_url;
	}
}
