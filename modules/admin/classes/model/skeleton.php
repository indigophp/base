<?php

namespace Admin;

trait Model_Skeleton
{
	protected static $_columns_cached = array();

	protected static $_list_cached = array();

	protected static $_form_cached = array();

	public static function properties($columns = false)
	{
		$properties = parent::properties();
		$class = get_called_class();

		// If not already determined
		if ( ! array_key_exists($class, static::$_columns_cached))
		{
			static::$_columns_cached[$class] = $properties;

			foreach ($properties as $key => $value)
			{
				if (strpos($key, '.') !== false or \Arr::get($value, 'eav', false) !== false)
				{
					unset($properties[$key]);
				}
			}

			static::$_properties_cached[$class] = $properties;
		}

		if ($columns === true)
		{
			return static::$_columns_cached[$class];
		}
		else
		{
			return $properties;
		}
	}

	/**
	 * Fetches a property description array, or specific data from it
	 *
	 * @param   string  property or property.key
	 * @param   mixed   return value when key not present
	 * @return  mixed
	 */
	public static function column($key, $default = null)
	{
		$class = get_called_class();

		$properties = static::properties(true);

		return \Arr::get($properties, $key, $default);
	}

	public static function view()
	{
		$properties = static::properties(true);

		return array_filter($properties, function($item) {
			return \Arr::get($item, 'view', true) === true;
		});
	}

	public static function lists()
	{
		$class = get_called_class();

		// If already determined
		if (array_key_exists($class, static::$_list_cached))
		{
			return static::$_list_cached[$class];
		}

		$properties = static::properties(true);

		foreach ($properties as $key => &$property)
		{
			if (\Arr::get($property, 'list.type', false) === false)
			{
				unset($properties[$key]);
				continue;
			}

			$property['list'] = \Arr::merge(
				\Arr::get($property, 'form', array()),
				\Arr::get($property, 'list')
			);
		}

		return static::$_list_cached[$class] = $properties;
	}

	public static function form($fieldset = false)
	{
		$class = get_called_class();

		// If already determined
		if (array_key_exists($class, static::$_form_cached))
		{
			$properties = static::$_form_cached[$class];
		}
		else
		{
			$properties = static::properties(true);

			foreach ($properties as $key => &$property)
			{
				if (\Arr::get($property, 'form.type', false) === false)
				{
					unset($properties[$key]);
					continue;
				}

				$property['form']['options'] = static::options($property['form']);
			}

			static::$_form_cached[$class] = $properties;
		}

		return $properties;
	}
}
