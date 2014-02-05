<?php
/**
 * Part of Fuel Core Extension.
 *
 * @package 	Fuel
 * @subpackage	Core
 * @version 	1.0
 * @author		Indigo Development Team
 * @license 	MIT License
 * @copyright	2013 - 2014 Indigo Development Team
 * @link		https://indigophp.com
 */

namespace Indigo\Orm;

use Orm\Model;
use Orm\Model_Temporal;

/**
 * Typing observer
 *
 * Sets a user_id property on insert
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Observer_Typing extends \Orm\Observer_Typing
{
	public static $type_methods = array(
		'time' => array(
			'before' => 'Indigo\\Orm\\Observer_Typing::time_encode',
			'after'  => 'Indigo\\Orm\\Observer_Typing::time_decode',
		),
	);

	public static function _init()
	{
		static::$type_methods = \Arr::merge(parent::$type_methods, static::$type_methods);
	}

	/**
	 * Takes formatted date string and transforms it into a DB timestamp
	 *
	 * @param  mixed value
	 * @param  array any options to be passed
	 * @return int|string
	 */
	public static function time_encode($var, array $settings)
	{
		if ( ! $var instanceof \Fuel\Core\Date)
		{
			if ($settings['data_type'] == 'time_mysql')
			{
				$var = \Date::create_from_string($var, \Arr::get($settings, 'data_format', 'mysql'));
			}
			else
			{
				$var = \Date::forge($var);
			}
		}

		return static::type_time_encode($var, $settings);
	}

	/**
	 * Takes a DB timestamp and converts it into a formatted date string
	 *
	 * @param  string value
	 * @param  array  any options to be passed
	 * @return string
	 */
	public static function time_decode($var, array $settings)
	{
		$var = parent::type_time_decode($var, $settings);

		return empty($var) ? null : $var->format(\Arr::get($settings, 'data_format', 'mysql'));
	}
}
