<?php

class Presenter_Bookinfo extends Presenter
{
	public function view()
	{
		$this->is_admin = Auth::has_access('right.admin');
		$this->comments = $this->book->comments;
	}
}
