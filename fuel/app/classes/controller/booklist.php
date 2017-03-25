<?php


use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Uri;
use Auth\Auth;
use Model\Book;

use Fuel\Core\Pagination;

class Controller_Booklist extends Controller_Template
{
	public function action_index()
	{
		$title = Input::get('title');
		$author = Input::get('author');
		$type = Input::get('type');

		if ($type == 'x') {
			$books_count = Model_Book::query()
				->where('title', 'like', '%'.$title.'%')
					->related('authors')
					->where('authors.name', 'like', '%'.$author.'%')
				->count();
		} else {
			$books_count = Model_Book::query()
				->where('title', 'like', '%'.$title.'%')
				->where('type', '=', $type)
					->related('authors')
					->where('authors.name', 'like', '%'.$author.'%')
				->count();
		}
		
		$num_links = 8;
		$show_first_and_last =  ($books_count / 10) > $num_links;
		
		$pagination = Pagination::forge('mypagination', 
				array(
						'total_items'    => $books_count,
						'per_page'       => 10,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));
		
		switch (Input::get('by')) {
			case "author": $order_type = "authors.name"; break;
			case "title":  $order_type = "title"; break;
			default:       $order_type = DB::expr('LENGTH(tag), tag'); break;
		}
		
		if ($type != 'x') {
			$books = Model_Book::query()
					->where('title', 'like', '%'.$title.'%')
					->where('type', '=', $type)
					->related('authors')
						->where('authors.name', 'like', '%'.$author.'%')
						->order_by($order_type)
					->rows_offset($pagination->offset)
					->rows_limit($pagination->per_page)
					->group_by('tag')
				->get();
		} else {
			$books = Model_Book::query()
					->where('title', 'like', '%'.$title.'%')
					->related('authors')
						->where('authors.name', 'like', '%'.$author.'%')
						->order_by($order_type)
					->rows_offset($pagination->offset)
					->rows_limit($pagination->per_page)
					->group_by('tag')
				->get();
		}
			
		/* 
		 * Have to go through book authors,
		 * since after changing author number 
		 * in the edit, only one author per book
		 * appears. This is a sort of fix to this issue.
		 */
		foreach ($books as $book)
			$book->get_authors();

		$current_view = '?' . Uri::build_query_string(Input::get());
		
		$data['pagination'] = $pagination;
		$data['books'] = $books;
		
		$this->template->short_head = true;
		$this->template->title = 'Wyszukane ksiażki ('. $books_count. ')';
		$this->template->content = Presenter::forge('booklist')
			->set('books', $books)
			->set('pagination', $pagination)
			->set('current_view', $current_view);
	}
}
