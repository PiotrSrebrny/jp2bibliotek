<?php



use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Fieldset;
use Fuel\Core\Validation;
use Auth\Auth;
use Model\Book;
use Fuel\Core\Model;
use Message\Message;

class ValidationTagRule {
	public static function _validation_unique_tag($val)
	{
		Validation::active()->set_message('unique_tag', 'Wybrany identifikator jest już wykorzystany');
		
		return !Model_Book::has_tag($val);
	}
}

class Controller_Book_Add extends Controller_Template
{
	public function action_index()
	{
		if (Auth::has_access('book.create') == false)
			Response::redirect('home');
		
		$this->template->title = 'Dodaj nową książkę';
		$this->template->menu = Presenter::forge('menu');
		
		/*
		 * Prepare validation instance
		 */
		$val = Validation::forge();
		
		if (!Input::post()) {
			$this->template->content = Presenter::forge('book/edit', 'view_empty');
			
		} else {
			
			/*
			 * Load authors names to the table
			 */
			$aidx = 0;
			$authors = array();
			$free_ids = array('a', 'b', 'c', 'd', 'e', 'f', 'g');
			
			foreach (Input::post() as $key => $value) {
				$split_key = explode('_', $key);
				
				if ($split_key[0] == 'author') {
					$authors[$aidx]['id'] = $split_key[1];
					
					$val->add_field($key, 'Autor', 'required');
					
					/* Remove from free id */
					for ($i = 0; $i < count($free_ids); $i++)
						if (strcmp($free_ids[$i], $split_key[1]) == 0)
							array_splice($free_ids, $i, 1);
							
					$authors[$aidx]['name'] = $value;
					$authors[$aidx]['active'] = true;
					$aidx++;
				}
			}
			
			if (Input::post('save_book')) {
				/*
				 * Simple validation
				 */
				$val->add_callable('ValidationTagRule');
				$val->add_field('title', 'Tytuł', 'required');
				$val->add_field('tag', 'Identifikator', 'required|unique_tag');
				
				/*
				 * If no error found in the form
				 * add the book to the DB
				 */
				if (!$val->run()) {
					Message::add_danger($val->show_errors());
					
					$data['error'] = $val->error();
					
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
					
					$this->template->content = Presenter::forge('book/edit', 'view_empty');
					$this->template->menu = Presenter::forge('menu')->set('addbook_pg', true);
					
					return;
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
		}		
		
		
		if (Input::post()) {
			$this->template->title = 'Dodaj nową książkę';
			$this->template->content = View::forge('book/edit', $data);
		}
	}
}
