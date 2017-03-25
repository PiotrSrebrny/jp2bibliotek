<?php

use Fuel\Core\Presenter;
use Fuel\Core\Uri;

class Presenter_Menu extends Presenter
{
	public function view()
	{
		$this->current_url = Uri::segment(1);
		$this->tabs = array();
		
		array_push($this->tabs, array('url' => 'home', 'name' => 'Start'));
		array_push($this->tabs, array('url' => 'search', 'name' => 'Szukaj'));
		
		if (Auth::check()) {
			if (Auth::has_access('book.create'))
				array_push($this->tabs, array('url' => 'addbook', 'name' => 'Dodaj książke'));
			
			if (Auth::has_access('right.admin') ||
				Auth::has_access('reader.any'))
				array_push($this->tabs, array('url' => 'admin', 'name' => 'Panel'));
			
			array_push($this->tabs, array('url' => 'account', 'name' => 'Moje konto'));
			array_push($this->tabs, array('url' => 'logout', 'name' => 'Wyloguj'));
			
		} else {
			array_push($this->tabs, array('url' => 'newaccount', 'name' => 'Nowe konto'));
			array_push($this->tabs, array('url' => 'login', 'name' => 'Zaloguj'));
		}		
	}
}
