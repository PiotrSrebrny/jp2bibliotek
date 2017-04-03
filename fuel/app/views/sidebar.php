<div>
<?php if (sizeof($this->menu) > 1) {?>
	<div class="col-sm-2">
	<ul class="nav nav-pills nav-stacked">
	<?php foreach ($this->menu as $item) {?>
		<li role="presentation" <?php if (Uri::string() == $item['link']) echo 'class="active"'; ?>>
			<?php echo Html::anchor($item['link'], $item['label']); ?>
		</li>
	<?php }	?> 
	</ul>
	</div>
<?php } ?>
<div class="col-sm-10" align="left">
<?php echo $this->content; ?>
</div>
</div>