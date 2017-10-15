<table class="table table-striped">
<tbody>
	
	<?php foreach ($borrows as $borrow) { 
	?>
		<tr>
			<td><?php echo $borrow->id; ?></td>
			<td><a href="/borrow/list/book/id/<?php echo $borrow->id . \Util\Uri::params()?>"><?php echo $borrow->book->title; ?></a></td>
			<td>
			<?php if ($borrow->reader != null) { ?> 
				<a href="/borrow/list/reader/id/<?php echo $borrow->reader_id . \Util\Uri::params()?>">
				<?php echo $borrow->reader->name; ?>
				</a>
			<?php } else { echo "brak"; } ?>
			</td>
			<td><?php echo Date::forge($borrow->borrowed_at)->format("%d.%m.%y"); ?></td>
			<td><?php echo $borrow->comment; ?></td>
		</tr>
	<?php } ?>
	</tbody>
	
</table>

<div class="text-center"><?php echo html_entity_decode($pagination); ?></div>
