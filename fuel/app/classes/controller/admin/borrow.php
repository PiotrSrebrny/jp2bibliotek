<?php


use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Date;
use Fuel\Core\Fieldset;
use Fuel\Core\Fieldset_Field;
use Fuel\Core\View;
use Auth\Auth;
use Message\Message;

class ValidationRules 
{
	public static function _validation_is_book_tag($tag)
	{
		Validation::active()->set_message('is_book_tag', 
				'Nie znaleziono książki o podanym identyfikatorze');

		return Model_Book::has_tag($tag);
	}
	
	public static function _validation_is_reader_id($id)
	{
		Validation::active()->set_message('is_reader_id', 
				'Nie znaleziono czytelnika o podanym identyfikatorze');

		return Model_Reader::find($id) != null;
	}
}

class Controller_Admin_Borrow extends Controller_Admin
{
	public function before()
	{
		return parent::before();
		
		if (!Auth::has_access("book.borrow"))
			return Response::redirect('login');
	}
	
	public function action_index()
	{
		$this->template->title = "Książki";
		$this->template->content = Html::anchor("admin/borrow/borrow", '<h4>Wypożycz książkę</h4>');
		$this->template->content .= Html::anchor("admin/borrow/list", '<h4>Lista wypożyczonych książek</h4>');
		$this->template->content .= Html::anchor("admin/borrow/return", '<h4>Zwróć książkę</h4>');
	}

	/************************************************************************/
	public function action_edit($id)
	{
		$borrow = Model_Borrow::query()->where('id', $id)->get_one();
		
		if ($borrow == null)
			return Response::redirect('404');
		
		$edit_form = Fieldset::forge();
		$edit_form->form()->set_attribute('class', 'form-horizontal');
		$edit_form->add('book_tag', 'Identyfikator książki', array('class' => 'form-control', 'readonly' => 'readonly'));
		$edit_form->add('book', 'Tytuł książki', array('class' => 'form-control', 'readonly' => 'readonly'));
		$edit_form->add('reader', 'Czytelnik', array('class' => 'form-control', 'readonly' => 'readonly'));
		$edit_form->add('borrowed_at', 'Pożyczono dnia', array('class' => 'form-control', 'readonly' => 'readonly'));
		if ($borrow->returned_at != 0)
		$edit_form->add('returned_at', 'Oddano dnia', array('class' => 'form-control', 'readonly' => 'readonly'));
		$edit_form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$edit_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-primary'));

		if (Input::post()) {
			$borrow->comment = Input::post('comment');
			$borrow->save();
			
			return Response::redirect('admin/borrow/info/' . $id);
		}

		$input = array (
				'book_tag' => $borrow->id,
				'book' => $borrow->book->title,
				'reader' => $borrow->reader->name,
				'borrowed_at' => Date::forge($borrow->borrowed_at)->format("%d.%m.%y"),
				'returned_at' => Date::forge($borrow->returned_at)->format("%d.%m.%y"),
				'comment' => $borrow->comment
		);
		
		$edit_form->populate($input);
		
		
		$this->template->content = $edit_form;
		$this->template->title = 'Edytuj komentarz';
	}
	
	private function borrow_execute()
	{
		$name_date = Input::post('reader');
		
		if (strpos($name_date, '(') == false) {
			Message::add_danger('Nie prawidłowy format czytelnika, oczekiwany: Imię Nazwisko (data urodzenia)');
			return;
		}
		
		list($name, $date) = explode('(', $name_date);
		
			
		$date = trim(substr($date, 0, -1));
		$name = trim($name);
		
		$reader = Model_Reader::get_by_name_and_date($name, $date);
		$book = Model_Book::get_by_tag(Input::post('book_tag'));
		
		if (($book == null) || ($reader == null)) {
			if ($book == null)
				Message::add_danger('Nie znaleziono książki');
					
			if ($reader == null)
				Message::add_danger('Nie znalezeiono czytelnika');
	
			return;
		}
		
		if ($book->is_borrowed()) {
			Message::add_danger('Książka jest aktualnie wypożyczona');
			return;
		}
		
		try {
			$new_borrow = new Model_Borrow();
			$new_borrow->reader_id = $reader->id;
			$new_borrow->book_id = $book->id;
			$new_borrow->borrowed_at = time();
			$new_borrow->comment = Input::post('comment');
			$new_borrow->returned_at = 0;
				
			$new_borrow->save();
			Response::redirect('admin/borrow');
		}
	
		catch (Exception $e) {
			Message::add_danger('Nie udało się wypożyczyć książki (' . $e->getCode() . ')');
		}
	}
	
	public function action_borrow()
	{
		$borrow_form = Fieldset::forge();
		$borrow_form->form()->set_attribute('class', 'form-horizontal');
		$borrow_form->add('book_tag', 'Identyfikator książki', array('class' => 'form-control'));
		$borrow_form->add('reader', 'Czytelnik', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'readers\')'));
		$borrow_form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$borrow_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Pożycz', 'class' => 'btn btn-primary'));

		if (Input::post()) {
			$val = Validation::forge($borrow_form);
		
			$val->add_callable('ValidationRules');
			$val->field('reader')
				->add_rule('required');
			$val->field('book_tag')
				->add_rule('required')
				->add_rule('is_book_tag');
			
			if (!$val->run()) {
				Message::add_danger($val->show_errors());
			} else {
				$this->borrow_execute();
			}
		}
		
		$borrow_form->repopulate();
		
		$this->template->content = $borrow_form;
		$this->template->title = 'Wypożycz książkę';
	}
	
	/************************************************************************/
	public function action_return($id)
	{
		$borrow = Model_Borrow::find($id);
		
		if ($borrow == null) {
			return Response::redirect('404');
		}
		
		$borrow->returned_at = time();
		$borrow->save();
			
		Response::redirect("admin/borrow/info/" .$id);
	}
	
	
	/************************************************************************/
	public function action_info($id)
	{
		$borrow = Model_Borrow::find($id);
		
		if ($borrow == null) 
			return Response::redirect('404');

		$button_list = array();
		
		if ($borrow->returned_at == 0)
			array_push($button_list, array('../return/' . $id, 'Zwróć'));
		
		array_push($button_list, array('../edit/' . $id, 'Edytuj'));
		
		$buttons = View::forge('buttons')
			->set('offset', 1)
			->set('buttons', $button_list);
		
		$this->template->content = View::forge('borrow/borrowinfo')
			->set('borrow', $borrow);
		
		$this->template->content .= $buttons;
		
	}
	
	/************************************************************************/
	public function action_list()
	{
		$borrows_count = \Model_Borrow::count_borrowed();

		$num_links = 8;
		$show_first_and_last =  ($borrows_count / 10) > $num_links;

		$pagination = Pagination::forge('mypagination',
				array(
						'total_items'    => $borrows_count,
						'per_page'       => 10,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));

		$borrows = Model_Borrow::get_borrowed();

		$current_view = '?' . Uri::build_query_string(Input::get());

		$this->template->title = 'Pożyczone ('. $borrows_count. ')';
		$this->template->content = View::forge('borrow/borrowlist')
			->set('borrows', $borrows)
			->set('pagination', $pagination)
			->set('current_view', $current_view);
	}
}
