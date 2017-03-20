<?php

namespace Fuel\Migrations;

class BookDB_Create_BookTable
{

	function up()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// only do this if it doesn't exist yet
		if ( ! \DBUtil::table_exists('books'))
		{
			// table users
			\DBUtil::create_table('books', array(
				'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
				'title' => array('type' => 'varchar', 'constraint' => 255),
				'tag' => array('type' => 'varchar', 'constraint' => 10),
				'type' => array('type' => 'char', 'constraint' => 1),
				'holder_id' => array('type' => 'int',  'constraint' => 11, 'default' => 0),
			), array('id'));
			
			// create extra index on book id
			\DBUtil::create_index('books', 'tag', 'tag', 'UNIQUE');
		}

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}

	function down()
	{
		// make sure the configured DB is used
		\DBUtil::set_connection(\Config::get('db.db_connection', null));

		// drop the admin_users table
		\DBUtil::drop_table('books');

		// reset any DBUtil connection set
		\DBUtil::set_connection(null);
	}
}
