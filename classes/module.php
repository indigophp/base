<?php

class Module extends \Fuel\Core\Module
{
	public static function load($module, $path = null)
	{
		$parent = parent::load($module, $path);

		if ($parent === true)
		{
			$path = static::exists($module);
			if (is_file($path .= 'bootstrap.php'))
			{
				\Fuel::load($path);
			}
		}

		return $parent;
	}
}
