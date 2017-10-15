<?php

use Message\Message;

class Controller_Reader_List extends Controller_Template
{
	public function before()
	{
		parent::before();
		
		if (!Auth::has_access("reader.access"))
			return Response::redirect('login');
	}

	/************************************************************************/
	static private function query_string()
	{
		$query = Uri::build_query_string(Input::get());
	
		if (strlen($query) > 0)
			return  '?' . $query;
			else
				return '';
	}
	
	/************************************************************************/
	public function action_index()
	{
		$reader_name = Input::get('reader');
	
		$readers_count = Model_Reader::query_like_name($reader_name)->count();
	
		$num_links = 8;
		$show_first_and_last =  ($readers_count / 10) > $num_links;
	
		$pagination = Pagination::forge('mypagination',
				array(
						'total_items'    => $readers_count,
						'per_page'       => 10,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));
	
		$order = is_null(Input::get('by')) ? 'id' : Input::get('by');
	
		$readers = Model_Reader::query_like_name($reader_name)
			->rows_offset($pagination->offset)
			->rows_limit($pagination->per_page)
			->order_by($order)
			->get();

		$this->template->title = 'Czytelnicy ('. $readers_count. ')';
		$this->template->content = View::forge('reader/readerlist')
			->set('readers', $readers)
			->set('pagination', $pagination)
			->set('current_view', $this->query_string());
	}
	
	
}