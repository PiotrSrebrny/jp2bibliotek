<?php

use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Date;
use Fuel\Core\Fieldset;
use Fuel\Core\Fieldset_Field;
use Fuel\Core\View;
use Auth\Auth;
use Util\Message;

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

class Controller_Borrow extends Controller_Template
{
	public function before()
	{
		return parent::before();

		if (!Auth::has_access("book.borrow"))
			return Response::redirect('login');
	}


	private function borrow_execute()
	{
		$readers = Model_Reader::query_has_name(Input::post('reader'));
		$book = Model_Book::get_by_tag(Input::post('book_tag'));

		if (!isset($book) || ($readers->count() == 0)) {
			if (!isset($book))
				Message::add_danger('Nie znaleziono książki');
					
			if ($readers->count() == 0)
				Message::add_danger('Nie znalezeiono czytelnika');
			
			return;
		}

		if ($readers->count() > 1) {
			Message::add_danger('Istniej więcej niż jeden czytelnik o podanym imieniu i nazwisku');
			return;
		}

		if ($book->is_borrowed()) {
			Message::add_danger('Książka jest aktualnie wypożyczona');
			return;
		}
		
		$reader = $readers->get_one();

		try {
			$new_borrow = new Model_Borrow();
			$new_borrow->reader_id = $reader->id;
			$new_borrow->book_id = $book->id;
			$new_borrow->borrowed_at = time();
			$new_borrow->comment = Input::post('comment');
			$new_borrow->returned_at = 0;

			$new_borrow->save();
			Message::add_success('Wypożyczono książkę "' . $book->title . '"' .
								 ' użytkownikowi ' . $reader->name);
			Response::redirect('borrow');
		}

		catch (Exception $e) {
			Message::add_danger('Nie udało się wypożyczyć książki (' . $e->getMessage() . ')');
		}
	}

	public function action_index()
	{
		$form = Fieldset::forge();
		$form->form()->set_attribute('class', 'form-horizontal');
		$form->add('book_tag', 'Identyfikator książki', array('class' => 'form-control'));
		$form->add('reader', 'Czytelnik', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'readers\')'));
		$form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Pożycz', 'class' => 'btn btn-primary'));

		if (Input::post()) {
			$val = Validation::forge($form);

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

		$form->repopulate();

		$this->template->title = 'Wypożycz książkę';
		$this->template->content = Presenter::forge('book')
		->set('content', $form);
	}	
}
