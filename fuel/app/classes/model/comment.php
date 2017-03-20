<?php

use Fuel\Core\DBUtil;
use Auth\Model;

class Model_Comment extends Orm\Model
{
	protected static $_properties = array(
			'id', 'text',
			'book_id', 'user_id', 'name', 
			'created_at', 'updated_at');
		

	protected static $_observers = array(
			'Orm\\Observer_CreatedAt', 
			'Orm\\Observer_UpdatedAt');
}

?>
