<?php

class Presenter_Book_Info extends Presenter
{
	public function view()
	{
		$this->comments = $this->book->comments;
	}
}
