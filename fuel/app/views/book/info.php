
<div class="row"> 
<div class="col-sm-1"></div>

<div class="col-sm-8">

<table class="table table-hover">

<?php 
	$author_id = 0;
	foreach($book->authors as $author) {
	$author_id++;
?>

<tr>
	<th class="col-sm-1 text-right">
		Autor<?php if (count($book->authors) > 1) echo ' '. $author_id ?>
	</th>
	<th class="col-sm-4">
		<?php echo $author['name']; ?>
	</th>

</tr>
<?php } ?>

<tr>
	<th class="col-sm-1 text-right">Identifikator</th>
	<th class="col-sm-4"><?php echo $book->tag ?></th>
</tr>

<?php
$type_choice = array(
  'x' => 'Nieznany',
  'a' => 'Dorośli',
  'd' => 'Dzieci',
  'r' => 'Religia',
  'f' => 'Filmy'
);
?>

<tr>
	<th class="col-sm-1 text-right">Typ</th>
	<th class="col-sm-4"><?php echo $type_choice[$book->type]; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Stan</th>
	<th class="col-sm-4"><?php echo $book->is_borrowed() ? "Wypożyczona" : "Dostępna"; ?></th>
</tr>

</table>

<div>
	<div class="col-sm-offset-1 col-sm-4">
	<?php 
	if (isset($return_url))
		echo Html::anchor($return_url, 'Wstecz', array('class' => 'btn btn-default'));		
	
	if (Auth::has_access('book.update'))
		echo Html::anchor('book/update/edit/'.$book->id . \Util\Uri::params(), 'Edytuj', array('class' => 'btn btn-default'));
	
	if (Auth::has_access('book.update'))
		echo Html::anchor('book/update/remove/'.$book->id .\Util\Uri::params(), 'Usuń', 
				array('class' => 'btn btn-default', 
					  'onclick' => "return confirm ('Czy napewno usunać?')"));
	?>
	</div>
</div>


<div>
<br><br>
<?php
	if (isset($comments) && count($comments) > 0)  {
?>
	<h3>Komentarze</h3>
<?php 
		foreach (array_reverse($comments) as $comment) {
			?>
			<div>
				<?php echo $comment->name; ?>
				<div class="pull-right">
					<?php echo date("Y-m-d", $comment->updated_at); ?>
				</div> 
				<div class="panel panel-default">
  				<div class="panel-body">
						<?php echo $comment->text; ?> 
  				</div>
				</div>
				<?php 
					if (($user_id == $comment->user_id) || 
						  (Auth::has_access("right.admin")))
						 { ?>
				
					<form action="<?php echo $my_url?>" method="post">
						<input value="<?php echo $comment->id ?>" name="delete" hidden="hidden">
						<input type="submit"  value="Usuń" class="btn btn-default">
					</form>
					<br>
				<?php } ?>
			</div> 
	<?php }
}
?> 
<br>
<?php if (Auth::check()) { ?>
<h3>Zostaw komentarz</h3>
	<div class="col-sm-offset-1 col-sm-10">
		<form method="post" id="comment">
			<textarea class="form-control" rows="1" form="comment" name="comment"></textarea>
			<br>
			<input type="submit" value="Wyślij" class="btn btn-default" id="form_text"/>
		</form>
	</div>
<?php } ?>
</div>
</div>
</div>
