<?php

use Auth\Auth;
class Presenter_Booklist extends Presenter
{
	public function view()
	{
		$this->is_admin = Auth::has_access('admin.right');
	}
}
