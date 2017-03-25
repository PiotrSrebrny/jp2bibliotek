<?php

use Auth\Auth;

use Model\Book;

use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Uri;
use Fuel\Core\Pagination;
use Fuel\Core\Controller;

class Controller_Dbquery extends Controller
{

	public function action_titles($text)
	{
		$books = Model_Book::find_by_some_chars($text, 7);
		
		$response = '<script>var auto=[';
		foreach ($books as $book)
			$response .= '{"label" : "' . $book['title'] . '"},';
		$response .= '];</script>';
		
		return Response::forge($response);
	}
	
	public function action_authors($text)
	{
		$authors = Model_Author::find_by_some_chars($text, 7);
		
		$response = '<script>var auto=[';
		foreach ($authors as $author)
			$response .= '{"label" : "' . $author['name'] . '"},';
		$response .= '];</script>';
		
		return Response::forge($response);
	}
	
	public function action_last_tag($type)
	{
		$last_tag = Model_Book::get_last_tag($type);		
	
	  return Response::forge($last_tag);
	}
}
