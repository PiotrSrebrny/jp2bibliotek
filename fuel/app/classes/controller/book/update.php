<?php

use Util\Message;
use Fuel\Core\Controller_Template;

class Controller_Book_Update extends Controller_Template
{
	public function before()
	{
		if (Auth::has_access('book.update') == false)
			Response::redirect('login');
		
		parent::before();
	}

	private function post($book)
	{
		$authors = array();
		
		foreach (Input::post() as $key => $value) {
			$split_key = explode('_', $key);
				
			if ($split_key[0] == 'author') {
		
				$author['id'] = $split_key[1];
				$author['name'] = $value;

				array_push($authors, $author);
			}
		}
		
		if (Input::post('save_book')) {
	
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
		}
		
		$data['tag_in'] = Input::post('tag');
		$data['title_in'] = Input::post('title');
		$data['type_in'] = Input::post('type');
		$data['authors_in'] = $authors;
		
		if (isset($error))
			$data['error'] = $error;
		
		return $data;
	}
	
	public function action_edit($book_id)
	{
		$book = Model_Book::find($book_id);
	
		if ($book == null)
			Response::redirect('home');
	
		if (Input::post()) {

			$this->template->content =
			View::forge('book/edit', $this->post($book))
				->set('return_url', 'book/info/view/' . $book_id. \Util\Uri::params())
				->set('submit_url', Uri::current() . \Util\Uri::params());
				
		} else {
			$this->template->content =
				Presenter::forge('book/edit', 'view_from_db')
			->set('book_id', $book_id)
			->set('return_url', 'book/info/view/' . $book_id . \Util\Uri::params())
			->set('submit_url', Uri::current() . \Util\Uri::params());
		}

		$this->template->title = 'Ksiazka';
	}
	

	public function action_remove($book_id)
	{
		$book = Model_Book::find($book_id);

		if ($book == null)
			Response::redirect('book');

		if ($book->removed)
			Response::redirect('book');
		
		$book->removed = true;
		$old_book_tag = $book->tag;
		$success = false;
		$i = 0;
		while ($i < 999) {
			try {
				$book->tag = '_' . $old_book_tag . '_' . $i++;
				$book->save();
			} catch (Exception $e) {
				continue;
			}
			$success = true;
			break;
		}

		if ($success)  {
			Message::add_success('Usunięto książkę');
		} else {
			Message::add_success('Nie udało się usunąć książki');
		}

		Response::redirect('book/list' . \Util\Uri::params());
	}
}