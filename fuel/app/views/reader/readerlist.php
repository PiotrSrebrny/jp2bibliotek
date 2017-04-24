<table class="table table-striped">
<tbody>
	
	<?php foreach ($readers as $reader) { 
	?>
		<tr>
			<td><?php echo $reader->id; ?></td>
			<td>
				<a href="info/<?php echo $reader->id . $current_view?>">
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
