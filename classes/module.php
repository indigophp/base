<?php

namespace Base;

class Module extends \Fuel\Core\Module
{

	public static function load($module, $path = null)
	{
		$parent = parent::load($module, $path);

		if ($parent === true)
		{
			$path = static::exists($module);
			\Finder::instance()->add_path($path, 1);
			if (\Finder::forge(array($path))->locate('', 'bootstrap') !== false)
			{
				\Fuel::load($path . 'bootstrap.php');
			}
		}

		return $parent;
	}
}