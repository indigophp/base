<?php

namespace Admin;

trait Model_Set
{
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
			switch ($property_data['data_type'])
			{
				case 'time_unix':
				case 'time_mysql':
					if ( ! $value instanceof \Fuel\Core\Date and ! empty($value))
					{
						if (is_numeric($value))
						{
							$value = \Date::forge($value);
						}
						else
						{
							try
							{
								$value = \Date::create_from_string($value, \Arr::get($property_data, 'data_format', 'mysql'));
							}
							catch (\UnexpectedValueException $e)
							{
								var_dump('var'); exit;
								// $value = \Date::create_from_string($value, 'mysql');
							}
						}
					}
					elseif(empty($value))
					{
						return;
					}
					break;
			}
		}

		return $value;
	}
}
