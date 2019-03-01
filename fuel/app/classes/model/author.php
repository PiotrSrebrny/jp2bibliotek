<?php 


use Fuel\Core\DBUtil;



class Model_Author extends Orm\Model
{	
	protected static $_properties = array('id', 'name');

	
	public static function get_author_by_name($author)
	{
		return parent::find('first', array(
				'where' => array(
						array('name', $author)
				)
		));
	}
	
	public static function find_by_some_chars($author, $num)
	{
		return parent::query()
		->where('name', 'like', '%'.$author.'%')
		->order_by('name')
		->limit($num)
		->get();
	}
}

?>