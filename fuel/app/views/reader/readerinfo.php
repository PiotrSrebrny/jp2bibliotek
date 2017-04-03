
<div class="row"> 
<div class="col-sm-1"></div>

<div class="col-sm-8">

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

<br>

<?php if ($reader->borrows != null) { ?>

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
	<?php foreach ($reader->borrows as $borrow) { ?>
		<tr>
			<th class="col-sm-1 text-center">
				<?php
				echo Date::forge($borrow->borrowed_at)->format("%d.%m.%y"); 
				?>
			</th>
			<th class="col-sm-1 text-center">
				<?php 
				if ($borrow->returned_at != 0) {
					echo Date::forge($borrow->returned_at)->format("%d.%m.%y");
				} ?>
			</th>
			<th class="col-sm-4 text-left">
				<a href="/book/borrow/info/<?php echo $borrow->id; ?>"> 
				<?php echo $borrow->book->title;?> 
				</a>
			</th>
		</tr>
	<?php } ?>
	</tbody>
	</table>
<?php }?>
