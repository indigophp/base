<?php

namespace Indigo\Base;

class Model_Enum_Meta extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'item_id',
		'key',
		'value',
	);

	protected static $_table_name = 'enum_meta';
}
