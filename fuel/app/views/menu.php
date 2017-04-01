<?php
use Fuel\Core\Uri;
?>
<ul class="nav nav-tabs">

<?php foreach ($tabs as $tab) {
	
	$urlSplit= explode('/', $tab['url']);
	$prefix = $urlSplit[0];
	?>

	<li <?php if ($prefix == $current_url) { ?> class="active" <?php } ?> > <?php echo Html::anchor($tab['url'], $tab['name']) ?> </li>
	
<?php }; ?>

</ul>
