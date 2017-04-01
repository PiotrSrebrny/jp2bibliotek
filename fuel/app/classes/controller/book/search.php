<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Fieldset;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Uri;
use Auth\Auth;
use Model\Book;
use Message\Message;

class Controller_Book_Search extends Controller_Template
{	
	public function action_index()
	{
		$book_types = array(
			'x' => 'Dowolny',
			'a' => 'Dorosli',
			'd' => 'Dzieci',
			'r' => 'Religia',
			'f' => 'Film');
		
		$form = Fieldset::forge();
		$form->form()->set_attribute('class', 'form-horizontal');
		
		$form->add('author', 'Autor', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'authors\')'));
		$form->add('title', 'Tytuł', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'titles\')'));
		$form->add('type', 'Rodzaj', array('class' => 'form-control', 'options' => $book_types, 'type' => 'select'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Szukaj', 'class' => 'btn btn-primary'));
		
		if (Input::post()) {
			$books_count = Model_Book::query_like(
									$form->input('title'), 
									$form->input('author'), 
									$form->input('type'))->count();
			
			if ($books_count > 0) {

				$uri_query = Uri::build_query_string(
								array('title' => $form->input('title')), 
								array('author' => $form->input('author')),
								array('type' => $form->input('type')),
								array('by' => 'title')
				);
				Message::add_success('Wyszukano ' . $books_count);
				Response::redirect('book/list/index?' . $uri_query);
			}
			
			Message::add_danger('Nie znaleziono książek o podanych kryteriach.');
		}
		 
		$this->template->title = 'Szukaj książek';
		$this->template->content = $form;
	}
}
