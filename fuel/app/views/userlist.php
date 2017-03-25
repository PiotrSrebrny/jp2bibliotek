<table class="table table-striped">
<tbody>
	
	<?php foreach ($users as $user) { 
		$groups_def = \Config::get('simpleauth.groups', false);
	?>
		<tr>
			<td><a href="user/edit/<?php echo $user->username?>"><?php echo $user->username; ?></a></td>
			<td><?php echo $user->email; ?></td>
			<td><?php echo $groups_def[$user->group]['name']; ?></td>
		</tr>
	<?php } ?>
	</tbody>
	
</table>

<div class="text-center"><?php echo html_entity_decode($pagination); ?></div>
