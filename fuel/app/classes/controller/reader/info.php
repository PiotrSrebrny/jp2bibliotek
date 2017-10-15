<?php


use Message\Message;

class Controller_Reader_Info extends Controller_Template
{
	public function before()
	{
		parent::before();
		
		if (!Auth::has_access("reader.access"))
			return Response::redirect('login');
	}
	
	/************************************************************************/
	static private function query_string()
	{
		$query = Uri::build_query_string(Input::get());
	
		if (strlen($query) > 0)
			return  '?' . $query;
		else
			return '';
	}
	
	/************************************************************************/
	static private function uri_build($page)
	{
		$uri_segments = Uri::segments();
	
		$count = count($uri_segments);
		$uri = '';
	
		$segments = $count - ($page == '..' ? 3 : 2);
	
		if ($segments <= 0) {
			return $uri;
		}
	
		for ($i = 0; $i < $segments; $i++) {
			$uri .= '/' . $uri_segments[$i];
		}
	
		if ($page != '..') {
			$uri .= '/' . $page;
		}
	
		$query = Uri::build_query_string(Input::get());
			
		if (strlen($query) > 0)
			$uri .= '?' . $query;
	
			return $uri;
	}
	
	/************************************************************************/
	public function action_id($id)
	{
		$reader = Model_Reader::query()->where('id', $id)->get_one();
	
		if ($reader == null)
			return Response::redirect('404');
	
		$buttons = View::forge('buttons')
			->set('offset', 1)
			->set('buttons', array(
					array($this->uri_build('edit/' . $id), 'Edytuj'),
					array($this->uri_build('..'), 'Wstecz')));


		$this->template->content = View::forge('reader/readerinfo')
			->set('reader', $reader)
			->set('buttons', $buttons)
			->set('current_view', $this->query_string());
	}
	
	/************************************************************************/
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
		$this->template->content .= View::forge('buttons')
			->set('offset', 1)
			->set('buttons', array(array($this->uri_build('id/' . $id), 'Wstecz')));
	}
}