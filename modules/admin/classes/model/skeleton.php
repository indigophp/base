<?php

namespace Admin;

trait Model_Skeleton
{
	protected static $_columns_cached = array();

	protected static $_options_cached = array();

	public static function properties($relations = false)
	{
		$properties = parent::properties();
		$class = get_called_class();

		// If already determined
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

		if ($relations === true)
		{
			return static::$_columns_cached[$class];
		}
		else
		{
			return static::$_properties_cached[$class];
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

		// If already determined
		if ( ! array_key_exists($class, static::$_columns_cached))
		{
			static::properties(true);
		}

		return \Arr::get(static::$_columns_cached[$class], $key, $default);
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
		$properties = static::properties(true);

		return array_filter($properties, function($item) {
			return \Arr::get($item, 'list.type', false) !== false;
		});
	}

	public static function form($fieldset = false)
	{
		$properties = static::properties(true);

		$properties = array_filter($properties, function($item) {
			return \Arr::get($item, 'form.type', false) !== false;
		});

		if ( ! empty(static::$_fieldsets) and $fieldset === true)
		{
			$fieldsets = static::$_fieldsets;

			foreach ($fieldsets as $key => $value)
			{
				$fieldsets[$key]['properties'] = \Arr::subset($properties, \Arr::get($fieldsets, $key.'.properties', array()), array());
			}

			return $fieldsets;
		}

		return $properties;
	}

	public function options($field)
	{
		$class = get_called_class();

		if (array_key_exists($class, static::$_options_cached) and array_key_exists($field, static::$_options_cached[$class]))
		{
			return static::$_options_cached[$class][$field];
		}

		$column = static::column($field, array());
		$column = \Arr::merge(\Arr::get($column, 'form', array()), \Arr::get($column, 'list', array()));

		// Get options and parse it if it is a Closure
		$options = array_key_exists('options', $column) ? $column['options'] : null;

		if ($options instanceof \Closure)
		{
			$options = $options($this);
		}

		// We have a string
		if (is_string($options))
		{
			// Is it comma delimited or the name of an enum?
			if (strpos($options, ','))
			{
				$options = explode(',', $options);
			}
			else
			{
				$options = \Model_Enum::query()
					->related('default')
					->related('items')
					->related('items.meta')
					->where('slug', $options)
					->get_one();

				if (is_null($options))
				{
					$options = array();
				}
				else
				{
					$options = $options->to_array();
					$options = \Arr::pluck($options['items'], 'name', 'id');
				}

			}
		}

		return static::$_options_cached[$class][$field] = $options;
	}

    /**
     * Set
     *
     * {@inheritdoc}
     *
     * In difference with the built-in set of the ORM's Model,
     * this method tries to take typing into account with string
     * values. (for example from '2013-12-03' creates a Date
     * object representing this date)
     *
     */
	public function set($property, $value = null)
	{
		if (is_array($property))
		{
			foreach ($property as $prop => $value) {
				$property[$prop] = $this->typing_map($prop, $value);
			}
		}
		else
		{
			$value = $this->typing_map($property, $value);
		}
		return parent::set($property, $value);
	}

	public function typing_map($property, $value)
	{
		$property_data = $this->property($property, array());
		if (isset($property_data['data_type']))
		{
			switch ($property_data['data_type']) {
				case 'time_unix':
				case 'time_mysql':
					if ( ! $value instanceof \Date and ! is_numeric($value))
					{
						// $value = \Date::create_from_string($value, \Arr::get($property_data, 'data_format', 'mysql'));
					}
					break;
			}
		}
		return $value;
	}

	public static function get_eav()
	{
		$eav = array();

		// get eav relations
		if (property_exists(get_called_class(), '_eav'))
		{
			// loop through the defined EAV containers
			foreach (static::$_eav as $rel => $settings)
			{
				// normalize the container definition, could be string or array
				if (is_string($settings))
				{
					$rel = $settings;
					$settings = array();
				}

				// determine attribute and value column names
				$eav[$rel] = array(
					'attribute' => \Arr::get($settings, 'attribute', 'attribute'),
					'value'     => \Arr::get($settings, 'value', 'value'),
				);
			}
		}

		return $eav;
	}
}
