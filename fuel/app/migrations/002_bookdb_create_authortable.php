<?php

namespace Fuel\Migrations;

class BookDB_Create_AuthorTable
{

	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it doesn't exist yet
		if ( ! \DBUtil::table_exists('authors'))
		{
			// table users
			\DBUtil::create_table('authors', array(
				'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
				'name' => array('type' => 'varchar', 'constraint' => 255)
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
		\DBUtil::drop_table('authors');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
