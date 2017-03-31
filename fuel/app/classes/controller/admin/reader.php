<?php


use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Fieldset;
use Fuel\Core\Fieldset_Field;
use Auth\Auth;
use Message\Message;

class Controller_Admin_Reader extends Controller_Admin
{
	
	public function action_index()
	{
		$this->template->title = "Czytelnicy";
		$this->template->content = Html::anchor("admin/reader/create", '<h4>Stwórz</h4>');
		$this->template->content .= Html::anchor("admin/reader/find", '<h4>Wyszukaj</h4>');
	}
	
	public function action_find()
	{
		$search_form = Fieldset::forge();
		$search_form->form()->set_attribute('class', 'form-horizontal');
		$search_form->add('reader', 'Czytelnik', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'readers\')'));
		$search_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Szukaj', 'class' => 'btn btn-primary'));
		
		if (Input::post()) {
			$reader = Input::post('reader');
			$reader_count = Model_Reader::count_by_name($reader);
				
			if ($reader_count > 0) {
				$uri = Uri::build_query_string(
						array('reader' => $reader));
				Response::redirect('admin/reader/list?' . $uri);
				
			} else {
				Message::add_danger('Nie znaleziono żadnego użytkownika');
			}
		}
		
		$this->template->title = "Wyszukaj czytelnika";
		$this->template->content = $search_form;
	}
	
	
	public function action_list()
	{
		if (!Auth::has_access("reader.read"))
			return Response::redirect('404');
		
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
	
		$readers = Model_Reader::get_by_name_subset($reader_name, 
									$pagination->offset, 
									$pagination->per_page);
				
		$current_view = '?' . Uri::build_query_string(Input::get());
	
		$this->template->title = 'Czytelnicy ('. $readers_count. ')';
		$this->template->content = View::forge('reader/readerlist')
			->set('readers', $readers)
			->set('pagination', $pagination)
			->set('current_view', $current_view);
	}
	
	public function action_create()
	{
		if (!Auth::has_access("reader.create"))
			return Response::redirect('404');

		$account_form = Fieldset::forge();
		
		$account_form->form()->set_attribute('class', 'form-horizontal');
		
		$account_form->add('id', 'Identyfikator', array('class' => 'form-control'));
		$account_form->add('fullname', 'Imię i nazwisko', array('class' => 'form-control'));
		$account_form->add('birth_date', 'Data urodzenia', array('class' => 'form-control'));
		$account_form->add('phone', 'Telefon', array('class' => 'form-control'));
		$account_form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$account_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Dodaj', 'class' => 'btn btn-primary'));
		
		if (Input::post()) {
			$val = Validation::forge($account_form);
				
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
					
					Response::redirect('admin/reader');
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
		
		$account_form->repopulate();
		
		$this->template->content = $account_form;
		$this->template->title = 'Nowy czytelnik';
	}

	public function action_delete($reader)
	{
		if (!Auth::has_access("reader.delete"))
			return Response::redirect('404');

		// FIXME: should this be implemented?
	}
	
	public function action_info($id)
	{
		if (!Auth::has_access("reader.read"))
			return Response::redirect('404');
		
		$reader = Model_Reader::query()->where('id', $id)->get_one();
		
		//$reader->borrows;
		$buttons = View::forge('buttons')
			->set('offset', 1)
			->set('buttons', array(array('../edit/' . $id, 'Edytuj')));
		
		$this->template->content = View::forge('reader/readerinfo')
			->set('reader', $reader)
			->set('buttons', $buttons);
	}
	
	public function action_edit($id)
	{
		if (!Auth::has_access("reader.create"))
			return Response::redirect('login');

		$reader = Model_Reader::query()->where('id', $id)->get_one();
		
		if ($reader == null) 
			return Response::redirect('404');
		
		$reader_form = Fieldset::forge();
		$reader_form->form()->set_attribute('class', 'form-horizontal');
		$reader_form->add('id', 'Identyfikator', array('class' => 'form-control', 'readonly' => 'readonly'));
		$reader_form->add('name', 'Imię i nazwisko', array('class' => 'form-control'));
		$reader_form->add('birth_date', 'Data urodzin', array('class' => 'form-control'));
		$reader_form->add('phone', 'Telefon', array('class' => 'form-control'));
		$reader_form->add('comment', 'Komentarz', array('class' => 'form-control'));
		$reader_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-primary'));
		
		if (Input::post())
		{
			$reader->name = $reader_form->field('name')->input();
			$reader->birth_date = $reader_form->field('birth_date')->input();
			$reader->phone = $reader_form->field('phone')->input();
			$reader->comment = $reader_form->field('comment')->input();
			$reader->save();
					
			\Message\Message::add_success('Wprowadzono zmiany');
		}
		
		$input = array (
				'id' => $reader->id,
				'name' => $reader->name,
				'birth_date' => $reader->birth_date,
				'phone' => $reader->phone,
				'comment' => $reader->comment,
		);
		
		$reader_form->populate($input);
		
		$this->template->title = 'Czytelnik ' . $reader->name;
		$this->template->content = $reader_form;
	}
}