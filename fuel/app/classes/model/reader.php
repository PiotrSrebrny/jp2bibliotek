<?php

use Fuel\Core\DBUtil;


class Model_Reader extends Orm\Model
{
	protected static $_properties = array('id', 'name', 'birth_date', 'phone', 'comment');

	protected static $_has_many = array('borrows');
	
	public static function query_like_name($name)
	{
		return parent::query()
			->where('name', 'like', '%'.$name.'%');
	}
	
	public function borrowed()
	{
		return \Model_Borrow::reader_borrowed($this->id);
	}
	
	public function returned()
	{
		return \Model_Borrow::reader_returned($this->id);
	}
	
	public static function get_by_name_and_date($name, $date)
	{
		return parent::query()
			->where('name', '=', $name)
			->where('birth_date', '=', $date)
			->order_by('name')
			->get_one();
	}
	
	public static function query_has_name($name) 
	{
		return parent::query()
			->where('name', '=', $name)
			->order_by('name');
	}
}

?>