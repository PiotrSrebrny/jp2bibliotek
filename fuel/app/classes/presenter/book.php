<?php

use Fuel\Core\View;

class Presenter_Book extends Presenter
{
	protected function set_view()
	{
		$this->_view = \View::forge('sidebar');
	}
	
	public function view()
	{
		$items = array();
		
		array_push($items, array('link' => 'book/search', 'label' => 'Wyszukaj'));
		
		if (Auth::has_access('book.add')) {
			array_push($items, array('link' => 'book/add', 'label' => 'Dodaj'));
		}
			
		if (Auth::has_access('book.borrow')) {
			array_push($items, array('link' => 'borrow', 
									 'label' => 'Wypożycz'));
			array_push($items, array('link' => 'borrow/list',
									 'label' => 'Lista pożyczonych'));
		}

		$this->menu = $items;
	}
}
