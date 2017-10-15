<table class="table table-striped">
<thead>
<tr>
	<th>
		<?php echo 'Identyfikator' . Html::anchor(Uri::update_query_string(array('by' => 'id')), '', array('class' => 'glyphicon glyphicon-sort')); ?>
	</th>

	<th>
		<?php echo 'Imię i nazwisko' . Html::anchor(Uri::update_query_string(array('by' => 'name')), '', array('class' => 'glyphicon glyphicon-sort')); ?>
	</th>
	<th>
		Telefon
	</th>
	</tr>
</thead>

<tbody>
	
	<?php foreach ($readers as $reader) { 
	?>
		<tr>
			<td><?php echo $reader->id; ?></td>
			<td>
				<a href="/reader/list/reader/id/<?php echo $reader->id . $current_view?>">
				<?php 
					echo $reader->name;
					if (isset($reader->birth_date) && ($reader->birth_date != "")) {
						echo ' (' . $reader->birth_date. ')';
					}
				?></a></td>
			<td><?php echo $reader->phone; ?></td>
		</tr>
	<?php } ?>
	</tbody>
	
</table>

<div class="text-center"><?php echo html_entity_decode($pagination); ?></div>
