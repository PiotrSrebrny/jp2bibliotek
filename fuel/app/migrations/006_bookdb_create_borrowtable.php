<?php

namespace Fuel\Migrations;

class BookDB_Create_BorrowTable
{
	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it doesn't exist yet
		if ( ! \DBUtil::table_exists('borrows'))
		{
			// table users
			\DBUtil::create_table('borrows', array(
				'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
				'reader_id' => array('type' => 'int', 'constraint' => 11),
				'book_id' => array('type' => 'int', 'constraint' => 11),
				'borrowed_at' => array('type' => 'int', 'constraint' => 11),
				'returned_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
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
		\DBUtil::drop_table('borrows');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
