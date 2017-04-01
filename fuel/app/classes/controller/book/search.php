<?php

use Fuel\Core\Controller_Template;
use Model\Book;

class Controller_Book_Search extends Controller_Template
{	
	public function action_index()
	{		 
		$this->template->title = 'Szukaj książek';
		$this->template->content = Presenter::forge('book/search');
	}
}
