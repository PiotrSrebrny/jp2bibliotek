<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Fieldset;
use Fuel\Core\Validation;
use Auth\Auth;
use Model\Book;
use Fuel\Core\Model;
use Util\Message;

class ValidationRule
{
	public static function _validation_type_valid($val)
	{
		Validation::active()
			->set_message(
				'type_valid',
				'Wybierz rodzaj');
	
		return 
			$val == 'a' ||
			$val == 'r' || 
			$val == 'd';
	}
	
	public static function _validation_tag_unique($val)
	{
		Validation::active()
			->set_message(
					'tag_unique', 
					'Wybrany identifikator jest już wykorzystany');
		
		return !Model_Book::has_tag($val);
	}
	
	public static function _validation_tag_syntax($val)
	{
		if ($val != ltrim($val)) {
			Validation::active()
				->set_message(
						'tag_syntax', 
						'Identyfikator nie może zawierać spacji na początku');
			return false;
		}
		
		if ($val != rtrim($val)) {
			Validation::active()
				->set_message(
						'tag_syntax', 
						'Identyfikator nie może zawierać spacji na końcu');
			return false;
		}
		
		$number = substr($val, 0, strlen($val) - 1);

		if (!is_numeric($number)) {
			Validation::active()
				->set_message(
						'tag_syntax', 
						'Identyfikator może składać się tylko z cyfr i litery na końcu');
			return false;
		}
		
		$type = $val[strlen($val) - 1];
		
		if ($type != 'a' && $type != 'r' && $type != 'd') {
			Validation::active()
				->set_message(
					'tag_syntax',
					'Indentyfikator może tylko kończyc się literą a, r, lub d');
			return false;
		}
		
		return true;
	}
}

class Controller_Book_Add extends Controller_Template
{
	public function action_index()
	{
		if (Auth::has_access('book.add') == false)
			Response::redirect('home');
		
		$this->template->title = 'Dodaj nową książkę';
		$this->template->menu = Presenter::forge('menu');
		
		/*
		 * Prepare validation instance
		 */
		$val = Validation::forge();
		
		if (!Input::post()) {
			$content = Presenter::forge('book/edit', 'view_empty');
			
		} else {
			/*
			 * Load authors names to the table
			 */
			$aidx = 0;
			$authors = array();
			
			foreach (Input::post() as $key => $value) {
				$split_key = explode('_', $key);
			
				if ($split_key[0] == 'author') {
					
					$val->add_field($key, 'Autor', 'required');
					
					$authors[$aidx]['id'] = $split_key[1];
					$authors[$aidx]['name'] = $value;
					$aidx++;
				}
			}

			/*
				* Simple validation
				*/
			$val->add_callable('ValidationRule');
			$val->add_field('title', 'Tytuł', 'required');
			$val->add_field('type', 'Rodzaj', 
					'required|type_valid');
			$val->add_field('tag', 'Identifikator', 
					'required|tag_unique|tag_syntax');
			
			/*
				* If no error found in the form
				* add the book to the DB
				*/
			if (!$val->run()) {
				Message::add_danger($val->show_errors());
				
				$data['error'] = $val->error();

				$data['tag_in'] = Input::post('tag');
				$data['title_in'] = Input::post('title');
				$data['type_in'] = Input::post('type');
				$data['authors_in'] = $authors;
				
				$content = View::forge('book/edit', $data);
				
			} else {
				
				$new_book = new Model_Book();
				
				/* Store book object */
				$new_book->title = Input::post('title');
				$new_book->tag = Input::post('tag');
				$new_book->type = Input::post('type');
				$new_book->holder_id = 0;
				
				/* Store all authors */
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
						
						$new_book->authors[] = $new_author;
						
					} else {
						$new_book->authors[] = $author_entry;
					}
				}
				
				$new_book->save();
			
				Message::add_success('Dodano ksiażkę');
// 						. Presenter::forge('bookinfo')
// 							->set('book_id', $new_book->id)
// 							->set('infoonly', true);
				
				$content = Presenter::forge('book/edit', 'view_empty');
				
			}
		}
				
		$this->template->content = Presenter::forge('book')->set('content', $content);
	}
}
