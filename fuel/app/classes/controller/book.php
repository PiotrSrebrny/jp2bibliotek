<?php

use Fuel\Core\Controller_Template;

class Controller_Book extends Controller_Template
{
	public function action_index()
	{
		Response::redirect("book/search");
	}
}