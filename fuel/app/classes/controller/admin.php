<?php


use Fuel\Core\Controller;
use Fuel\Core\Controller_Template;
use Auth\Auth;
use Fuel\Core\Presenter;
use Fuel\Core\Response;
use Fuel\Core\Html;



class Controller_Admin extends Controller_Template
{
	protected $is_admin = false;
	
	public function before()
	{
		if (Auth::check())
			$this->is_admin = Auth::has_access("right.admin");
		
		return parent::before();
	}
	
	public function action_index()
	{
		if ($this->is_admin == false)
			return Response::redirect("404");
		
		$this->template->content = Html::anchor("admin/user", '<h4>Użytkownicy</h4>');
		$this->template->title = "Panel";
	}
}