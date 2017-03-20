<?php

namespace Fuel\Migrations;

class BookDB_Create_CommentTable
{

	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it doesn't exist yet
		if ( ! \DBUtil::table_exists('comments'))
		{
			// table users
			\DBUtil::create_table('comments', array(
				'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
				'text' => array('type' => 'text'),
				'book_id' => array('type' => 'int', 'constraint' => 11),
				'user_id' => array('type' => 'int', 'constraint' => 11),
				'name' => array('type' => 'varchar', 'constraint' => 255),
				'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
				'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
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
		\DBUtil::drop_table('comments');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
