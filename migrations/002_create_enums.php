<?php

namespace Fuel\Migrations;

class Create_enums
{
	public function up()
	{
		\DBUtil::create_table('enums', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name'        => array('constraint' => 64, 'type' => 'varchar'),
			'slug'        => array('constraint' => 67, 'type' => 'varchar'),
			'description' => array('type' => 'text', 'null' => true),
			'default_id'  => array('type' => 'int', 'default' => 1, 'unsigned' => true, 'null' => true),
			'active'      => array('type' => 'tinyint', 'default' => 1),
			'read_only'   => array('type' => 'tinyint', 'default' => 0),
		), array('id'));

		\DBUtil::create_index('enums', 'slug', 'index_enums_on_slug', 'UNIQUE');

		\DBUtil::create_table('enum_items', array(
			'id'          => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'enum_id'     => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'item_id'     => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'name'        => array('constraint' => 64, 'type' => 'varchar'),
			'slug'        => array('constraint' => 67, 'type' => 'varchar'),
			'description' => array('type' => 'text', 'null' => true),
			'active'      => array('type' => 'tinyint', 'default' => 1),
			'sort'        => array('constraint' => 11, 'type' => 'int', 'null' => true),
		), array('id'));

		\DBUtil::create_index('enum_items', 'slug', 'index_enums_on_slug', 'UNIQUE');
		\DBUtil::create_index('enum_items', 'item_id', 'index_enum_items_on_item_id', 'INDEX');
		\DBUtil::create_index('enum_items', array('enum_id', 'item_id'), 'index_enum_items_on_enum_id_item_id', 'UNIQUE');

		\DBUtil::add_foreign_key('enum_items', array(
			'constraint' => 'fk_index_enum_items_on_enum_id',
			'key' => 'enum_id',
			'reference' => array(
				'table' => 'enums',
				'column' => 'id',
			),
			'on_update' => 'CASCADE',
			'on_delete' => 'CASCADE'
		));
	}

	public function down()
	{
		\DBUtil::drop_table('enum_items');
		\DBUtil::drop_table('enums');
	}
}