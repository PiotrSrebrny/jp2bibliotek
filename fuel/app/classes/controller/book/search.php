<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\View;
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
		
		$form1 = Fieldset::forge('by_name');
		$form1->form()->set_attribute('class', 'form-horizontal');
		
		$form1->add('author', 'Autor', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'authors\')'));
		$form1->add('title', 'Tytuł', array('class' => 'form-control', 'onkeyup' => 'lookUp(this, \'titles\')'));
		$form1->add('type', 'Rodzaj', array('class' => 'form-control', 'options' => $book_types, 'type' => 'select'));
		$form1->add('submit', ' ', array('type' => 'submit', 'value' => 'Szukaj', 'class' => 'btn btn-primary'));
		
		
		$form2 = Fieldset::forge('by_tag');
		$form2->form()->set_attribute('class', 'form-horizontal');
		
		$form2->add('tag', 'Identyfikator', array('class' => 'form-control'));
		$form2->add('submit', ' ', array('type' => 'submit', 'value' => 'Szukaj', 'class' => 'btn btn-primary'));
		
		if (Input::post()) {
			if ($form2->input('tag') !== null) {
		
				$book = Model_Book::get_by_tag($form2->input('tag'));
		
				if ($book !== null)
					Response::redirect('book/info/view/' . $book->id);
				else
					Message::add_danger('Nie znaleziono książki');
				
		
			} else {
		
				$books_count = Model_Book::query_like(
						$form1->input('title'),
						$form1->input('author'),
						$form1->input('type'))->count();
		
				if ($books_count > 0) {

					$uri_query = Uri::build_query_string(
							array('title' => $form1->input('title')),
							array('author' => $form1->input('author')),
							array('type' => $form1->input('type')),
							array('by' => 'title')
							);

					Message::add_success('Wyszukano ' . $books_count);
					Response::redirect('book/list/index?' . $uri_query);
				}

				Message::add_danger('Nie znaleziono książek o podanych kryteriach');
			}
		}
		
		$view = \View::forge('book\search')
			->set('form_by_name', $form1)
			->set('form_by_tag', $form2);
		
		$this->template->title = 'Wyszukaj książki';
		$this->template->content = Presenter::forge('book')
			->set('content', $view->render());
	}
}
