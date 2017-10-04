<?php 


use Fuel\Core\DBUtil;


class Model_Borrow extends Orm\Model
{	
	protected static $_properties = array('id', 'book_id', 'reader_id', 
										  'borrowed_at', 'returned_at', 'comment');
	
	protected static $_belongs_to = array('book', 'reader');
	
	public static function all()
	{
		return parent::query()->where('returned_at','=', 0);
	}
	
	public static function is_borrowed($book_id)
	{
		return 
			(parent::query()
				->where('book_id','=',$book_id)
				->where('returned_at','=',0)
				->count() != 0);
	}
	
	public static function reader_borrowed($reader_id)
	{
		return parent::query()
		->where('reader_id', '=', $reader_id)
		->where('returned_at','=',0)
		->order_by('borrowed_at','desc');
	}
	
	public static function reader_returned($reader_id)
	{
		return parent::query()
		->where('reader_id', '=', $reader_id)
		->where('returned_at','!=',0)
		->order_by('borrowed_at','desc');
	}
	
	public static function book_all($book_id)
	{
		return parent::query()
		->where('book_id', '=', $book_id)
		->order_by('borrowed_at','desc');
	}
	
}

?>
