<?php

namespace Fuel\Migrations;

class BookDB_Create_ReaderTable
{

	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it doesn't exist yet
		if ( ! \DBUtil::table_exists('readers'))
		{
			// table users
			\DBUtil::create_table('readers', array(
				'id' => array('type' => 'int', 'constraint' => 11),
				'name' => array('type' => 'varchar', 'constraint' => 255),
				'birth_date' => array('type' => 'varchar', 'constraint' => 14),
				'phone' => array('type' => 'varchar', 'constraint' => 16),	
				'comment' => array('type' => 'text'),
			), array('id'));
		}

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}

	function down()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// drop the admin_users table
		\DBUtil::drop_table('readers');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
