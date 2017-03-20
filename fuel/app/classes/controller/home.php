<?php

use Fuel\Core\Controller_Template;

use Fuel\Core\Response;
use Fuel\Core\View;


class Controller_Home extends Controller_Template
{
	public function action_index()
	{
		$this->template->content = 
			View::forge('home')
				->set('comments', Model_Comment::query()->order_by('updated_at', 'DESC')->limit(4)->get());
	}
	
	public function action_404()
	{
		return Response::forge(View::forge('404'), 404);
	}
	
}
