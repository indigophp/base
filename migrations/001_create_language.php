<?php

namespace Fuel\Migrations;

class Create_language
{
	public function up()
	{
		\DBUtil::create_table('language', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'lang' => array('constraint' => 4, 'type' => 'varchar', 'default' => 'en'),
			'model' => array('constraint' => 255, 'type' => 'varchar'),
			'item_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'key' => array('constraint' => 255, 'type' => 'varchar'),
			'value' => array('type' => 'text'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('language');
	}
}
