<ul class="nav nav-tabs">

<?php foreach ($tabs as $tab) {?>

	<li <?php if ($tab['url'] == $current_url) { ?> class="active" <?php } ?> > <?php echo Html::anchor($tab['url'], $tab['name']) ?> </li>
	
<?php }; ?>

</ul>
