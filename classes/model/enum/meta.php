<?php

namespace Indigo\Base;

class Model_Enum_Meta extends \Orm\Model
{
	protected static $_belongs_to = array(
		'item' => array(
			'key_from' => 'item_id',
			'model_to' => 'Model_Enum_Item',
		)
	);

	protected static $_properties = array(
		'id',
		'item_id',
		'key',
		'value',
	);

	protected static $_table_name = 'enum_meta';
}
