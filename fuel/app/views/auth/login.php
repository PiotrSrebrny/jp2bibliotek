<?php

echo Form::open(array("class"=>"form-horizontal"));

?>

  <fieldset>
    <div class="form-group">
      <?php echo Form::label('Użytkownik', 'username', array('class'=>'col-sm-2 control-label')); ?>
      <div class="col-sm-3">
        <?php echo Form::input('username', Input::get('username',''),array('class'=>'form-control')); ?>
      </div>
    </div>
    <div class="form-group">
	      <?php echo Form::label('Hasło', 'password', array('class'=>'col-sm-2 control-label')); ?>
      <div class="col-sm-3">
        <?php echo Form::input('password', Input::get('password',''),array('class'=>'form-control', 'type' => 'password')); ?>
      </div>
    </div>
		<div class="form-group">
			
			<div class="col-sm-offset-2 col-sm-4">
				<?php echo Form::submit('login', 'Zaloguj', array('class' => 'btn btn-default')); ?>
      </div>
		</div>
    
  </fieldset>
  
  <?php  if ($recovery == true) { ?>
  	<a href="login/recovery">Odzyskaj hasło</a> 
  <?php }?>

<?php echo Form::close(); ?>
