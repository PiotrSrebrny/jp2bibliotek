<?php


use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Fieldset;
use Fuel\Core\Fieldset_Field;
use Auth\Auth;
use Message\Message;
use Fuel\Core\Controller_Template;

class Controller_Reader extends Controller_Template
{
	public function before()
	{
		if (!Auth::has_access("reader.access"))
			return Response::redirect('login');
		
		parent::before();
	}
	
	public function action_index()
	{
		Response::redirect('reader/find');
	}
	
	public function action_find()
	{
		$form = Fieldset::forge();
		$form->form()->set_attribute('class', 'form-horizontal');
		$form->add('reader', 'Czytelnik', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'readers\')'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Szukaj', 'class' => 'btn btn-primary'));
		
		if (Input::post()) {
			$reader = Input::post('reader');
			$reader_count = Model_Reader::count_by_name($reader);
				
			if ($reader_count > 0) {
				$uri = Uri::build_query_string(
						array('reader' => $reader));
				Response::redirect('reader/list?' . $uri);
				
			} else {
				Message::add_danger('Nie znaleziono żadnego użytkownika');
			}
		}
		
		$this->template->title = "Wyszukaj czytelnika";
		$this->template->content = Presenter::forge('reader')
			->set('content', $form);
	}
	
	
	public function action_list()
	{
		$reader_name = Input::get('reader');

		$readers_count = Model_Reader::count_by_name($reader_name);
	
		$num_links = 8;
		$show_first_and_last =  ($readers_count / 10) > $num_links;
	
		$pagination = Pagination::forge('mypagination',
				array(
						'total_items'    => $readers_count,
						'per_page'       => 10,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));
	
		$readers = Model_Reader::query_by_name($reader_name)
			->rows_offset($pagination->offset)
			->rows_limit($pagination->per_page)
			->get();
				
		$current_view = '?' . Uri::build_query_string(Input::get());
	
		$this->template->title = 'Czytelnicy ('. $readers_count. ')';
		$this->template->content = View::forge('reader/readerlist')
			->set('readers', $readers)
			->set('pagination', $pagination)
			->set('current_view', $current_view);
	}
	
	public function action_create()
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
			$val->field('birth_date')
				->add_rule('required');
				//->add_rule('valid_date', '%d.%m.%Y');
				
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

	public function action_delete($reader)
	{
		if (!Auth::has_access("reader.delete"))
			return Response::redirect('404');

		// FIXME: should this be implemented?
	}
	
	public function action_info($id)
	{
		$reader = Model_Reader::query()->where('id', $id)->get_one();
		
		if ($reader == null)
			return Response::redirect('404');
		
		$buttons = View::forge('buttons')
			->set('offset', 1)
			->set('buttons', array(array('/reader/edit/' . $id, 'Edytuj')));
		
		
		$this->template->content = View::forge('reader/readerinfo')
			->set('reader', $reader)
			->set('buttons', $buttons);
	}
	
	public function action_edit($id)
	{
		$reader = Model_Reader::query()->where('id', $id)->get_one();
		
		if ($reader == null) 
			return Response::redirect('404');
		
		$form = Fieldset::forge();
		$form->form()->set_attribute('class', 'form-horizontal');
		$form->add('id', 'Identyfikator', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('name', 'Imię i nazwisko', array('class' => 'form-control'));
		$form->add('birth_date', 'Data urodzin', array('class' => 'form-control'));
		$form->add('phone', 'Telefon', array('class' => 'form-control'));
		$form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-primary'));
		
		if (Input::post())
		{
			$reader->name = $form->field('name')->input();
			$reader->birth_date = $form->field('birth_date')->input();
			$reader->phone = $form->field('phone')->input();
			$reader->comment = $form->field('comment')->input();
			$reader->save();
					
			Message::add_success('Wprowadzono zmiany');
			Response::redirect('/reader/info/' . $id);
		}
		
		$input = array (
				'id' => $reader->id,
				'name' => $reader->name,
				'birth_date' => $reader->birth_date,
				'phone' => $reader->phone,
				'comment' => $reader->comment,
		);
		
		$form->populate($input);
		
		$this->template->title = 'Czytelnik ' . $reader->name;
		$this->template->content = $form;
	}
}