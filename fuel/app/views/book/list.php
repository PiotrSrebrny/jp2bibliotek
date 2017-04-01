<table class="table table-striped">
	<thead>
	<tr>
			<?php if (Auth::has_access('book.update')) { ?>
				<th>
					<?php echo 'Identifikator ' . Html::anchor(Uri::update_query_string(array('by' => 'tag')), '', array('class' => 'glyphicon glyphicon-sort')); ?>
				</th>
			<?php } ?>
			
			<th>
				<?php echo 'Tytuł ' . Html::anchor(Uri::update_query_string(array('by' => 'title')), '', array('class' => 'glyphicon glyphicon-sort')); ?>
			</th>
			<th>
				<?php echo 'Autor ' . Html::anchor(Uri::update_query_string(array('by' => 'author')), '', array('class' => 'glyphicon glyphicon-sort')); ?>
			</th>
			</tr>
	</thead>
	<tbody>
	
	<?php foreach ($books as $book): ?>
		<tr>
			<?php if (Auth::has_access('book.update')) echo '<td>'.$book->tag.'</td>' ?>
			<td><?php echo $book->title; ?></td>
			<td><?php 
				$first = true;
				foreach ($book->authors as $author) {
					/* Separate names with commas */
					if ($first)	$first = false;	else echo ', ';
					
					echo $author->name;
				}
				?>
			</td>
			<td>
				<span>
					<?php 
					
						echo Html::anchor('book/info/view/'.$book['id'] . $current_view, '', array('class' => 'glyphicon glyphicon-eye-open'));
						
						if (Auth::has_access('book.update'))
							echo Html::anchor('book/info/edit/'.$book['id'] . $current_view, '', array('class' => 'btn glyphicon-wrench'));
						
						if (Auth::has_access('book.delete'))
							echo Html::anchor('book/info/remove/'.$book['id'] . $current_view, '', 
									array('class' => 'glyphicon glyphicon-trash', 
											  'onclick' => "return confirm ('Czy napewno usunać?')")); 
						
						?>
				</span> 
			</td> 
		</tr>
	<?php endforeach; ?>
	</tbody>
	
</table>

<div class="text-center"><?php echo html_entity_decode($pagination); ?></div>
