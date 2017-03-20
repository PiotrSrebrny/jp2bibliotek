<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Fieldset;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Uri;
use Auth\Auth;
use Model\Book;
use Message\Message;

class Controller_Search extends Controller_Template
{	
	public function action_index()
	{
		$book_types = array(
			'x' => 'Dowolny',
			'a' => 'Dorosli',
			'd' => 'Dzieci',
			'r' => 'Religia',
			'f' => 'Film');
		
		$search_form = Fieldset::forge();
		$search_form->form()->set_attribute('class', 'form-horizontal');
		
		$search_form->add('author', 'Autor', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'authors\')'));
		$search_form->add('title', 'Tytuł', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'titles\')'));
		$search_form->add('type', 'Rodzaj', array('class' => 'form-control', 'options' => $book_types, 'type' => 'select'));
		$search_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Szukaj', 'class' => 'btn btn-primary'));
		
		if (Input::post()) {		
			$books_count = Model_Book::count_like_title_author($search_form->input('title'), $search_form->input('author'), $search_form->input('type'));
			
			if ($books_count > 0) {

				$uri_query = Uri::build_query_string(
					array('title' => $search_form->input('title')), 
					array('author' => $search_form->input('author')),
					array('type' => $search_form->input('type')),
					array('by' => 'title')
				);
				Message::add_success('Wyszukano ' . $books_count);
				Response::redirect('booklist/index?' . $uri_query);
			}
			
			Message::add_danger('Nie znaleziono książek o podanych kryteriach.');
		}
		 
		$this->template->title = 'Szukaj książek';
		$this->template->content = $search_form;
	}
}
