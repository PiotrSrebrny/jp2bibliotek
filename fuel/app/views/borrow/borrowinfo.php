
<div class="row"> 
<div class="col-sm-1"></div>

<div class="col-sm-8">

<table class="table table-hover">

<tr>
	<th class="col-sm-1 text-right">Identifikator</th>
	<th class="col-sm-4"><?php echo $borrow->id ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Imie i nazwisko</th>
	<th class="col-sm-4"><?php echo $borrow->reader->name; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Telefon</th>
	<th class="col-sm-4"><?php echo $borrow->reader->phone; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Książka</th>
	<th class="col-sm-4"><?php echo $borrow->book->title; ?></th>
</tr>

<tr>
	<th class="col-sm-1 text-right">Wypożyczono</th>
	<th class="col-sm-4"><?php echo Date::forge($borrow->borrowed_at)->format("%d.%m.%y"); ?></th>
</tr>
<?php if (($borrow->returned_at != 0)) { ?>
	<tr>
		<th class="col-sm-1 text-right">Oddano</th>
		<th class="col-sm-4"><?php echo Date::forge($borrow->returned_at)->format("%d.%m.%y"); ?></th>
	</tr>
<?php } ?>

<tr>
	<th class="col-sm-1 text-right">Komentarz</th>
	<th class="col-sm-4"><?php echo $borrow->comment; ?></th>
</tr>
</table>

<?php if (isset($return_url)) { ?>
	<div>
		<div class="col-sm-offset-1 col-sm-4">
			<?php echo Html::anchor($return_url, 'Wstecz', array('class' => 'btn btn-default')); ?>		
		</div>
	</div>
<?php } ?>
</div>
</div>
