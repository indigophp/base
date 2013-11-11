<?php

namespace Fuel\Migrations;

class Create_enums
{
	public function up()
	{
		\DBUtil::create_table('enums', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'enum'        => array('constraint' => 32, 'type' => 'varchar', 'unsigned' => true),
			'item_id'     => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'name'        => array('constraint' => 64, 'type' => 'varchar'),
			'slug'        => array('constraint' => 67, 'type' => 'varchar'),
			'description' => array('type' => 'text', 'null' => true),
			'default'     => array('type' => 'tinyint', 'default' => 0),
			'active'      => array('type' => 'tinyint', 'default' => 1),
			'read-only'   => array('type' => 'tinyint', 'default' => 0),
			'sort'        => array('constraint' => 11, 'type' => 'int', 'null' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('enums');
	}
}