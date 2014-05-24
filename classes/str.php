<?php
/**
 * Part of the Indigo framework.
 *
 * @package    Indigo
 * @subpackage Base
 * @version    1.0
 * @author     Indigo Development Team
 * @license    MIT License
 * @copyright  2013 - 2014 Indigo Development Team
 * @link       https://indigophp.com
 */

namespace Indigo\Base;

/**
 * Str extension
 *
 * Custom translation
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Str extends \Fuel\Core\Str
{
	/**
	 * Alternative way to use strtr
	 *
	 * @param  string        $string String to parse
	 * @param  array|string  $array  Params to replace or from string
	 * @param  string|null    $to    To string or null
	 * @return string
	 */
	public static function trans($string, $array = array(), $to = null)
	{
		if (is_string($string) and empty($array) === false)
		{
			if ( ! is_array($array))
			{
				$array = array($array => $to);
			}

			return strtr($string, $array);
		}
		else
		{
			return $string;
		}
	}
}
