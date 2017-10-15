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
			$reader_count = Model_Reader::query_like_name($reader)->count();
				
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
	

	public function action_delete($reader)
	{
		if (!Auth::has_access("reader.delete"))
			return Response::redirect('404');

		// FIXME: should this be implemented?
	}
}