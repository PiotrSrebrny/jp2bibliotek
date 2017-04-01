<?php

class Presenter_Book_Edit extends Presenter
{
	public function view()
	{

	}
	
	public function view_empty()
	{
		$authors_in = array();
		$authors_in[0]['name'] = '';
		$authors_in[0]['active'] = true;
		$authors_in[0]['id'] = 'a';
		
		$this->tag_in = '';
		$this->title_in = '';
		$this->type_in = 0;
		$this->author_count = 1;
		$this->authors_in = $authors_in;
	}
	
	public function view_from_db()
	{
		$book = Model_Book::find($this->book_id);
		
		if ($book == null)
			Response::redirect('404');
		
		$authors = $book->authors;
		
		$authors_in = array();
		
		foreach ($authors as $author) {
			$author_in['name'] = $author->name;
			$author_in['id'] = $author->id;
			$author_in['active'] = false;
			array_push($authors_in, $author_in);
		}
		
		$author_count = count($authors);
				
		/*
		 * Fill in fields
 	   */
	  $this->tag_in = $book->tag;
	  $this->title_in = $book->title;
	  $this->type_in = $book->type;
	  $this->author_count = $author_count;
	  $this->authors_in = $authors_in;
	}
}
