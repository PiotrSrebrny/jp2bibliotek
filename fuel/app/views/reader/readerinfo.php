<?php
use Fuel\Core\Uri;
?>

<div class="row"> 
<div class="col-sm-1"></div>

<div class="col-sm-8">
<div class="container">
<h3>Czytelnik</h3>
<table class="table table-hover">
<tr>
	<th class="col-sm-1 text-right">Identifikator</th>
	<th class="col-sm-4"><?php echo $reader->id ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Imie i nazwisko</th>
	<th class="col-sm-4"><?php echo $reader->name; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Data urodzenia</th>
	<th class="col-sm-4"><?php echo $reader->birth_date; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Telefon</th>
	<th class="col-sm-4"><?php echo $reader->phone; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Komentarz</th>
	<th class="col-sm-4"><?php echo $reader->comment; ?></th>
</tr>
</table>

<?php echo $buttons; ?>
</div>

<?php if ($reader->borrows != null) { ?>
	<div class="container">

	<h3>Książki</h3>
	<table class="table table-hover">
	<thead>
	<tr>
		<th class="col-sm-1 text-center">Wypożyczono</th>
		<th class="col-sm-1 text-center">Oddano</th>
		<th class="col-sm-1 text-left">Tytuł</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$returns = $reader->returned()->get();
	$borrows = $reader->borrowed()->get();
	?>
	<?php foreach ($borrows as $borrow) { ?>
		<tr>
			<th class="col-sm-1 text-center">
				<?php
				echo Date::forge($borrow->borrowed_at)->format("%d.%m.%y"); 
				?>
			</th>
			<th class="col-sm-1 text-center"></th>
			<th class="col-sm-4 text-left">
				<a href="<?php echo Uri::current() . '/id/' .  $borrow->id; ?>"> 
				<?php echo $borrow->book->title;?> 
				</a>
			</th>
		</tr>
	<?php } ?>
	<?php foreach ($returns as $return) { ?>
		<tr>
			<th class="col-sm-1 text-center">
				<?php
				echo Date::forge($return->borrowed_at)->format("%d.%m.%y"); 
				?>
			</th>
			<th class="col-sm-1 text-center">
				<?php 
				echo Date::forge($return->returned_at)->format("%d.%m.%y");
				?>
			</th>
			<th class="col-sm-4 text-left">
				<a href="<?php echo Uri::current() . '/id/' . $return->id; ?>"> 
				<?php echo $return->book->title;?> 
				</a>
			</th>
		</tr>
	<?php } ?>
	</tbody>
	</table>
	</div>
<?php }?>
