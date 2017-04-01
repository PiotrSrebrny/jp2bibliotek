<?php

class Presenter_Book_Info extends Presenter
{
	public function view()
	{
		$this->is_admin = Auth::has_access('right.admin');
		$this->comments = $this->book->comments;
	}
}
