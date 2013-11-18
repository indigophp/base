<?php

namespace Fuel\Migrations;

class Create_db_config
{
	public function up()
	{
		\DBUtil::create_table('config', array(
			'identifier' => array('constraint' => 100, 'type' => 'char'),
			'config'     => array('type' => 'longtext'),
			'hash'       => array('constraint' => 13, 'type' => 'char'),
		), array('identifier'));
	}

	public function down()
	{
		\DBUtil::drop_table('config');
	}
}