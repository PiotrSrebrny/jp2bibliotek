<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\Uri;
use Model\Book;
use Auth\Auth;

class Controller_Book_Info extends Controller_Template
{
	public function action_view($book_id)
	{		
		$book = Model_Book::find($book_id);
		
		if ($book === null) {
			$this->template->title = 'Nie znaleziono książki';
			return;
		}
		
		if (Auth::check())
			list(, $user_id) = Auth::get_user_id();
		else
			$user_id = -1;
		
		if (Input::post())
		{
			if (!Auth::check())
				Response::redirect('404');
			
			if (Input::post('delete'))
			{
				$comment = Model_Comment::find(Input::post('delete'));
				if ($comment)
				{
					if (($comment->user_id == $user_id) || 
					    (Auth::has_access("right.admin")))
						$comment->delete();
				}
			}
			
			if (Input::post('comment')) 
			{	
				$comment = new Model_Comment();
				$comment->text = Input::post('comment');
				$comment->user_id = $user_id;
				$comment->name =  Auth::get('fullname');
				$comment->book_id = $book_id;
				
				$comment->save();
				
				$book->comments[] = $comment;
				$book->save();
			}
		}
		
		$this->template->title = strlen($book->title) == 1 ? "Brak tytułu" : $book->title;
		$this->template->content = 
			Presenter::forge('book/info')
				->set('book', $book)
				->set('user_id', $user_id)
				->set('my_url', Uri::current() . \Util\Uri::params())
				->set('return_url', 'book/list?' . Uri::build_query_string(Input::get()));
		
	}
}
