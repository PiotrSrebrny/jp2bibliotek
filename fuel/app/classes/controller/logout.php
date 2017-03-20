<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Auth\Auth;
use Message\Message;

class Controller_Logout extends Controller_Template
{
	public function action_index()
	{
		if (Auth::check())
			Auth::logout();
	
		Message::add_success('Wylogowano');
	}
}
