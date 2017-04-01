<?php

use Auth\Auth;
class Presenter_Book_List extends Presenter
{
	public function view()
	{
		$this->is_admin = Auth::has_access('admin.right');
	}
}
