<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <title>Biblioteka im. św. Jana Pawła II</title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::css('jquery-ui.css'); ?>
    <?php echo Asset::js('jquery-1.11.2.js'); ?>
    <?php echo Asset::js('jquery-ui.js'); ?>
    <?php echo Asset::js('autocomplete.js'); ?>
    
		<style>
		.jhead {
		  background-color: #05ABC5;
		  color: #FFFED2;
		}
		</style>
</head>
<body>

		<div class="jumbotron jhead">
			<center><h1>Biblioteka im. św. Jana Pawła II</h1></center>
		</div>
	<div class="container">
		
		<div class="col-sm-12">
        <h4><?php echo Presenter::forge('menu'); ?></h4>
    </div>
    
		<?php 
		$succes_msgs = \Util\Message::get_success(); 
		if (isset($succes_msgs) && count($succes_msgs) > 0) {
	  ?>
		<div class="alert alert-success">
			<?php foreach ($succes_msgs as $success)
				echo html_entity_decode($success, ENT_QUOTES, 'utf-8') . html_tag('br'); 
			?>
		</div>
		<?php 
		} 
		?>
		<?php
		$danger_msgs = \Util\Message::get_danger(); 
		if (isset($danger_msgs) && count($danger_msgs) > 0) {
		?>
		<div class="alert alert-danger">
			<?php foreach ($danger_msgs as $danger)
				 echo html_entity_decode($danger, ENT_QUOTES, 'utf-8'); ?>
		</div>
		<?php 
		}
		?>
		
    <div class="col-sm-12">
        <h2><?php if (isset($title)) { echo $title; } ?></h2>        
		</div>
		
		<div class="col-sm-12">
        <?php if (isset($content)) { echo html_entity_decode($content, ENT_QUOTES, 'utf-8'); } ?>
    </div>
    
    <footer>
    <p class="pull-right">Based on FuelPHP framework</p>
    </footer>
  </div>
  
  <div id="ac_div">
  </div>
</body>
</html>
