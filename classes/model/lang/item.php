<?php

namespace Orm;

class Model_Lang_Item extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'lang',
		'model',
		'item_id',
		'key',
		'value',
	);

	protected static $_table_name = 'language';
}
