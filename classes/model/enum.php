<?php

namespace Indigo\Base;

class Model_Enum extends \Orm\Model
{
	protected static $_eav = array(
		'meta' => array(
			'attribute' => 'key',
			'value'     => 'value',
		)
	);

	protected static $_has_many = array(
		'meta' => array(
			'model_to'       => 'Model_Enum_Meta',
			'cascade_delete' => true,
		),
	);

	protected static $_observers = array(
		'Orm\\Observer_Slug' => array('source' => 'name'),
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert')
		)
	);

	protected static $_properties = array(
		'id',
		'enum',
		'item_id' => array('data_type' => 'int'),
		'name',
		'slug',
		'description',
		'default' => array(
			'default'   => 0,
			'data_type' => 'boolean',
			'min'       => 0,
			'max'       => 1,
		),
		'active' => array(
			'default'   => 1,
			'data_type' => 'boolean',
			'min'       => 0,
			'max'       => 1,
		),
		'read-only' => array(
			'default'   => 0,
			'data_type' => 'boolean',
			'min'       => 0,
			'max'       => 1,
		),
		'sort' => array('data_type' => 'int'),
	);

	protected static $_sort = true;

	protected static $_table_name = 'enums';

	public function _event_before_insert()
	{
		$this->item_id = $this->query()->where('enum_id', $this->enum_id)->max('item_id') + 1;
		static::$_sort === true and $this->sort = $this->query()->where('enum_id', $this->enum_id)->max('sort') + 10;
	}
}
