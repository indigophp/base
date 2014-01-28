<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Indigo\Base;

use Orm\Model;
use Orm\Observer;

/**
 * Typing observer.
 *
 * Runs on load or save, and ensures the correct data type of your ORM object properties.
 */
class Observer_Tracker extends Observer
{
	public static function before_update(Model $model)
	{
		if ($user_id = \Auth::get_user_id())
		{
			$model->user_id = $user_id[1];
		}
	}

	public static function before_insert(Model $model)
	{
		if ($model instanceof \Orm\Model_Temporal)
		{
			if ($model->{$model->temporal_property('end_column')} !== $model->temporal_property('max_timestamp')) {
				return false;
			}
		}
		return static::before_update($model);
	}

}


