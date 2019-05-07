<?php

namespace Fuel\Migrations;

class BookDB_BookTable_AddRemoved
{

	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it exists 
		if ( \DBUtil::table_exists('books'))
		{
			\DBUtil::add_fields('books', array(
				'removed' => array('type' => 'boolean', 'default' => false),
			));
		}

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}

	function down()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// drop the admin_users table
		\DBUtil::drop_fields('books', 'removed');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
