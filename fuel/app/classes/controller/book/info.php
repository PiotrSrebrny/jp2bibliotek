<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Uri;
use Model\Book;
use Auth\Auth;
use Message\Message;
use Oil\Command;

class Controller_Book_Info extends Controller_Template
{
	/*
	 * Helper function to get/store return URL
	 */
	private function get_return_url()
	{
		return 'book/list/index?' . Uri::build_query_string(Input::get());
	}
	
	private function get_my_url()
	{
		return Uri::current() . '?' . Uri::build_query_string(Input::get());
	}
	
	public function action_view($book_id)
	{		
		$book = Model_Book::find($book_id);
		
		if ($book) {
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
					->set('my_url', $this->get_my_url())
					->set('return_url', $this->get_return_url());
			
		} else {
			$this->template->title = 'Nie znaleziono książki';
		}
	}
	
	public function action_edit($book_id)
	{
		if (Auth::has_access('book.update') == false)
			Response::redirect('home');
		
		$book = Model_Book::find($book_id);

		if ($book == null)
			Response::redirect('home');
		
		if (Input::post()) {
			$authors = array();
			$free_ids = array('a', 'b', 'c', 'd');
				
			foreach (Input::post() as $key => $value) {
				$split_key = explode('_', $key);
			
				if ($split_key[0] == 'author') {
					
					$author['id'] = $split_key[1];
					$author['name'] = $value;
					
					if (is_numeric($split_key[1])) {
						$author['active'] = false;
						
					} else {
						$author['active'] = true;
						
						/* Remove from free id */
						for ($i = 0; $i < count($free_ids); $i++)
							if (strcmp($free_ids[$i], $split_key[1]) == 0)
								array_splice($free_ids, $i, 1);
						
					}
					array_push($authors, $author);
				}
			}
			
			if (Input::post('save_book')) {
				/*
				 * Simple validation
				 */
				if (!Input::post('tag')) {
					$error['tag'] = true;
					
				} else {
					if ((Model_Book::has_tag(Input::post('tag'))) &&
							(Input::post('tag') != $book->tag)) 
						$error['tag'] = true;
				}
				
				if (!Input::post('title'))
					$error['title'] = true;
				
				foreach ($authors as $author)
					if (strlen($author['name']) == 0)	
						$error['author_'.$author['id']] = true;
	
				/*
				 * If no error found in the form
				 * add the book to the DB
				 */
				if (isset($error)) {
					Message::add_danger('Uzupełnij/popraw zaznaczone pola');
					
				} else {
					/* Store book object */
					$book->title = Input::post('title');
					$book->tag = Input::post('tag');
					$book->type = Input::post('type');
					$book->holder_id = 0;
					
					/* Store all authors */
					$book_author_ids = array();
					
					foreach ($authors as $author) {
						/*
						 * The numeric ids carry former authors 
						 * of the book, only alphabetical 
						 * ids have new authors.
						 */
						if (is_numeric($author['id'])) {
							array_push($book_author_ids, $author['id']);
							continue;
						}
						
						$author_entry = Model_Author::get_author_by_name($author['name']);
						
						/*
						 *  Was the author found in the database?
						 */
						if ($author_entry == null) {
							/*
							 * No, create a new entry
							 */
							$new_author = new Model_Author();
							$new_author->name = $author['name']; 
							$new_author->save();
							
							$book->authors[] = $new_author;
							
							array_push($book_author_ids, $new_author->id);
						} else {
							
							$book->authors[] = $author_entry;
							
							array_push($book_author_ids, $author_entry->id);
						}
						
					}
					
					/*
					 * Drop those authors that were removed
					 * from the book author list
					 */
					foreach ($book->authors as $author) {
						for ($i = 0; $i < count($book_author_ids); $i++)
							if ($book_author_ids[$i] == $author->id)
								break;
						
					  if ($i == count($book_author_ids)) {
					  	/*
					  	 * Author not found on the list 
					  	 * must be removed
					  	 */
					  	unset($book->authors[$author->id]);
					  }
					}
				
					$book->save();
						
					Message::add_success('Wprowadzono zmiany');
				}
				
				
			} else if (Input::post('add_author')) {
				
				if (count($free_ids) > 0)
					array_push($authors, array(
							'name' => '',
							'id' => $free_ids[0],
							'active' => true
					));
			
			} else {
				/*
				 * Was author deletion requested?
				 */
				foreach (Input::post() as $key => $value) {
					$split_key = explode('_', $key);
				
					if ($split_key[0] == 'delauthor')
						for ($i = 0; $i < count($authors); $i++)
							if (strcmp($authors[$i]['id'], $split_key[1]) == 0) {
								array_splice($authors, $i, 1);
								
								break;
							}
					
				}
				
			}
			
			/*
			 * Fill in fields
			 */
			$data['tag_in'] = Input::post('tag');
			$data['title_in'] = Input::post('title');
			$data['type_in'] = Input::post('type');
			$data['authors_in'] = $authors;
				
			if (isset($error))
				$data['error'] = $error;

			$this->template->content = 
				View::forge('book/edit', $data)
					->set('return_url', $this->get_return_url())
					->set('submit_url', Uri::current() . '?' . Uri::build_query_string(Input::get()));
		 
		}	else {
				
			$this->template->content = 
				Presenter::forge('book/edit', 'view_from_db')
					->set('book_id', $book_id)
					->set('return_url', $this->get_return_url())
					->set('submit_url', Uri::current() . '?'  . Uri::build_query_string(Input::get()));
		}
		
		$this->template->title = 'Ksiazka';
	}
	
	public function action_remove($book_id)
	{
		if (Auth::has_access('book.update') == false)
			Response::redirect('home');
	
		$book = Model_Book::find($book_id);
	
		if ($book == null)
			Response::redirect('home');
		
		$book->delete();
		
		Response::redirect($this->get_return_url());
	}
}
