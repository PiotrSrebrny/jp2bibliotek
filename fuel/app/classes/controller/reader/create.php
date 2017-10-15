<?php

use Util\Message;

class Controller_Reader_Create extends Controller_Template
{
	public function before()
	{
		parent::before();
		
		if (!Auth::has_access("reader.access"))
			return Response::redirect('login');	
	}
	
	public function action_index()
	{
		$form = Fieldset::forge();
	
		$form->form()->set_attribute('class', 'form-horizontal');
	
		$form->add('id', 'Identyfikator', array('class' => 'form-control'));
		$form->add('fullname', 'Imię i nazwisko', array('class' => 'form-control'));
		$form->add('birth_date', 'Data urodzenia', array('class' => 'form-control'));
		$form->add('phone', 'Telefon', array('class' => 'form-control'));
		$form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Dodaj', 'class' => 'btn btn-primary'));
	
		if (Input::post()) {
			$val = Validation::forge($form);
	
			$val->field('id')->add_rule('required');
			$val->field('fullname')
			->add_rule('required')
			->add_rule('trim');
	
			if (!$val->run()) {
				Message::add_danger($val->show_errors());
	
			} else {
	
				try {
					$new_reader = new Model_Reader();
					$new_reader->id = Input::post('id');
					$new_reader->name = Input::post('fullname');
					$new_reader->phone = Input::post('phone');
					$new_reader->birth_date = Input::post('birth_date');
					$new_reader->comment = Input::post('comment');
						
					$new_reader->save();
						
					Message::add_success('Dodano czytelnika');
					Response::redirect('reader');
				}
	
				catch (Exception $e) {
					if ($e->getCode() == 23000 /* ER_DUP_KEY */) {
						Message::add_danger('Czytelnik o podanym identyfikatorze już istnieje');
					} else {
						Message::add_danger('Nie udało się stowrzyc czytelnika (' . $e->getCode() . ')');
					}
				}
			}
		}
	
		$form->repopulate();
	
		$this->template->title = 'Dodaj czytelnika';
		$this->template->content =
		Presenter::forge('reader')->set('content', $form);
	}
}