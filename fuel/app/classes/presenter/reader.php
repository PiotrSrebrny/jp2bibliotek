<?php

use Fuel\Core\View;

class Presenter_Reader extends Presenter
{
	protected function set_view()
	{
		$this->_view = \View::forge('sidebar');
	}

	public function view()
	{
		$items = array();

		array_push($items, array('link' => 'reader/find', 'label' => 'Wyszukaj'));
		array_push($items, array('link' => 'reader/create', 'label' => 'Dodaj'));

		$this->menu = $items;
	}
}
