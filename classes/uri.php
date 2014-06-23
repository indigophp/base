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
 * Uri extension
 *
 * Get admin URL
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Uri extends \Fuel\Core\Uri
{
	/**
	 * Gets the admin URL
	 *
	 * @param boolean $absolute Whether to return absolute URL
	 *
	 * @return  string
	 */
	public static function admin($absolute = true)
	{
		static $relative_url;
		static $absolute_url;

		if (empty($relative_url))
		{
			$relative_url = \Config::get('indigo.admin_url');
			$absolute_url = static::base() . $relative_url;
		}
		return $absolute ? $absolute_url : $relative_url;
	}
}
