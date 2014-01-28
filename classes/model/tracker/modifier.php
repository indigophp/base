<?php

namespace Indigo\Base;

trait Model_Tracker_Modifier
{
	protected static $_user_field;

	public static function _init()
	{
		parent::_init();

		static::$_user_field = static::$_user_field ?: 'user_id';

		if ( ! static::property(static::$_user_field, false))
		{
			throw new \FuelException('There is no user_field defined on this model: ' . get_called_class());
		}
	}

	public function save($cascade = null, $use_transaction = false)
	{
		if ($user_id = \Auth::get_user_id())
		{
			// $this->user_id = $user_id[1];
		}

		return parent::save($cascade, $use_transaction);
	}
}
