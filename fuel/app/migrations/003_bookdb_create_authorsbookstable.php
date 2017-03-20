<?php

namespace Fuel\Migrations;

class BookDB_Create_AuthorsBooksTable
{

	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it doesn't exist yet
		if ( ! \DBUtil::table_exists('authors_books'))
		{
			// table users
			\DBUtil::create_table('authors_books', array(
				'book_id' => array('type' => 'int', 'constraint' => 11),
				'author_id' => array('type' => 'int', 'constraint' => 11)
			), array('book_id', 'author_id'));
		}

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}

	function down()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// drop the admin_users table
		\DBUtil::drop_table('authors_books');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
