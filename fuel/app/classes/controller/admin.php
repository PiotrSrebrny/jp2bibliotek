<?php


use Fuel\Core\Controller;
use Fuel\Core\Controller_Template;
use Auth\Auth;
use Fuel\Core\Presenter;
use Fuel\Core\Response;
use Fuel\Core\Html;



class Controller_Admin extends Controller_Template
{
	public function before()
	{
		if (!Auth::check())
			Response::redirect("login");
			
		return parent::before();
	}
	
	public function action_index()
	{
		$this->template->content = "";
		
		if (Auth::has_access("reader.read"))
			$this->template->content .= Html::anchor("admin/reader", '<h4>Czytelnicy</h4>');
		
		if (Auth::has_access("book.borrow"))
			$this->template->content .= Html::anchor("admin/borrow", '<h4>Książki</h4>');
		
	    if (Auth::has_access("right.admin"))
			$this->template->content .= Html::anchor("admin/user", '<h4>Użytkownicy</h4>');
				
		$this->template->title = "Panel";
	}
}
