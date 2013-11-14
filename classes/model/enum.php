<?php

namespace Indigo\Base;

class Model_Enum extends \Orm\Model
{
	protected static $_has_many = array(
		'items' => array(
			'model_to'       => 'Model_Enum_Item',
			'cascade_delete' => true,
		),
	);

	protected static $_has_one = array(
		'default' => array(
			'key_from' => array('id', 'default_id'),
			'key_to'   => array('enum_id', 'item_id'),
			'model_to' => 'Model_Enum_Item',
		),
	);

	protected static $_observers = array(
		'Orm\\Observer_Slug' => array('source' => 'name'),
		'Orm\\Observer_Typing',
	);

	protected static $_properties = array(
		'id',
		'name',
		'slug',
		'description',
		'default_id' => array(
			'default'   => 1,
			'data_type' => 'int',
		),
		'active' => array(
			'default'   => 1,
			'data_type' => 'boolean',
			'min'       => 0,
			'max'       => 1,
		),
		'read_only' => array(
			'default'   => 0,
			'data_type' => 'boolean',
			'min'       => 0,
			'max'       => 1,
		),
	);

	protected static $_table_name = 'enums';

	public function add_item($name = null, $default = false, $eav = array())
	{
		$model = Model_Enum_Item::forge(array('name' => $name));
		$model->set($eav);
		$model->enum = $this;

		$model->save();

		if ($default === true)
		{
			$this->default = $model->id;
			$this->save();
		}
	}

	public static function enum()
	{
		return static::query()->get_one();
	}
}
