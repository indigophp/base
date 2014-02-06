<?php

/**
 * This class registers available widgets per area through Widget::instance('arename')
 *
 * @package Base
 * @version 1.0
 * @author IndigoPHP
 * @license MIT License
 * @copyright 2013, IndigoPHP
 */

class Widget
{
	protected static $_instances = [];
	protected static $_instance  = null;

	/**
	 * A simple return new static();
	 *
	 * @return Widget A new Widget instance.
	 */
	public static function forge()
	{
		return new static();
	}

	/**
	 * Create or return the widget container instance
	 *
	 * @param	void
	 * @access	public
	 * @return	Widget object
	 */
	public static function instance($instance = null)
	{
		if ($instance !== null)
		{
			if ( ! array_key_exists($instance, static::$_instances))
			{
				$instance = static::$_instances[$instance] = static::forge();
				return $instance;
			}

			return static::$_instances[$instance];
		}

		if (static::$_instance === null)
		{
			static::$_instance = static::forge();
		}

		return static::$_instance;
	}

	protected $_widgets = [];

	public function add($name, $widget_controller_route)
	{

	}

	/**
	 * Fires a HMVC request to the widget, and returns the body, does not echo anything.
	 *
	 * @throws HttpNotFoundException When the widget cannot be found.
	 * @param  string $name The name of the widget.
	 * @return string       The body of the rendered widget
	 */
	public function render($name)
	{
		
	}

}