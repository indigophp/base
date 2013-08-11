<?php

namespace Base;

class Model_Language extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'lang',
		'model',
		'item_id',
		'key',
		'value',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'language';
}
