<?php 


use Fuel\Core\DBUtil;


class Model_Book extends Orm\Model
{	
	protected static $_properties = array('id', 'title', 'type', 'tag', 'holder_id');
	
	protected static $_many_many = array('authors');
	protected static $_has_many = array('comments');
	
	/************************************************************************/
	public function is_borrowed()
	{
		return Model_Borrow::is_borrowed($this->id);
	}
	
	/************************************************************************/
	public static function has_tag($tag)
	{
		$exist = parent::find('first', array(
				'where' => array(
						array('tag', $tag)
				)
			));
		
		return ($exist != null);
	}
	
	/************************************************************************/
	public static function get_by_tag($tag)
	{
		return parent::find('first', array(
				'where' => array(
						array('tag', $tag)
				)));
	}
	
	/************************************************************************/
	public function get_authors()
	{
		return parent::query()
			->where('id','=', $this->id)
			->related('authors')
			->get();
	}
	
	
	/************************************************************************/
	public static function find_by_some_chars($title, $num)
	{
		return parent::query()
			->where('title', 'like', '%'.$title.'%')
			->limit($num)
			->order_by('title')
			->get();
	}
	
	/************************************************************************/
	public static function get_last_tag($type)
	{
		$last_book = parent::find('last', array(
				'where' => array(
						array('type', $type)
				)
			));
		
		return $last_book['tag'];
	}
	
	/************************************************************************/
	public static function query_like($title, $author, $type)
	{
		$query = parent::query();
		$query = $query->where('title', 'like', '%'.$title.'%');
		
 		if ($type != 'x') {
 			$query = $query->where('type', '=', $type);
 		}
			
		$query = $query->related('authors')
					->where('authors.name', 'like', '%'.$author.'%');
		
		return $query;
	}
}

?>
