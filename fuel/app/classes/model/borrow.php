<?php 


use Fuel\Core\DBUtil;


class Model_Borrow extends Orm\Model
{	
	protected static $_properties = array('id', 'book_id', 'reader_id', 
										  'borrowed_at', 'returned_at', 'comment');
	
	protected static $_belongs_to = array('book', 'reader');
	
	public static function get_borrowed()
	{
		return parent::query()->where('returned_at','=', 0)->get();
	}
	
	public static function count_borrowed()
	{
		return parent::query()->where('returned_at','=', 0)->count();
	}

	public static function is_borrowed($book_id)
	{
		return 
			(parent::query()
				->where('book_id','=',$book_id)
				->where('returned_at','=',0)
				->count() != 0);
	}
	
	public static function reader_borrowed($reader)
	{
		return parent::query()
		->where('reader_id', '=', $reader->id)
		->where('returned_at','=',0)
		->order_by('borrowed_at','desc');
	}
	
	public static function reader_returned($reader)
	{
		return parent::query()
		->where('reader_id', '=', $reader->id)
		->where('returned_at','!=',0)
		->order_by('borrowed_at','desc');
	}
	
}

?>
