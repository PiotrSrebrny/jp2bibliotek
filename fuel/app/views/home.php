
Strona biblioteki przy kosciele Św. Olafa w Oslo


<?php
	if (isset($comments) && count($comments) > 0)  {
?>
	<h3>Komentarze</h3>
<?php 
		foreach ($comments as $comment) {
			?>
			<div class="col-sm-offset-1 col-sm-10">
				<div>
					<h4>
					<?php echo Model_Book::find($comment->book_id)->title;?>
					</h4>
				</div>
					<div class="panel panel-default">
	  				<div class="panel-body">
							<?php echo $comment->text; ?> 
	  				</div>
					<div class="pull-right">
					<?php echo $comment->name; ?>
					</div> 
					</div>
			</div>
<?php }} ?> 