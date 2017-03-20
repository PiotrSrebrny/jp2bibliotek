
<?php

echo Form::open(array("class"=>"form-horizontal"));

?>

  <fieldset>
    <div class="form-group">
      <?php echo Form::label('Tytuł', 'title', array('class'=>'col-sm-2 control-label')); ?>
      <div class="col-sm-4">
      	<?php 
      		echo Form::input('title', Input::get('title',''),
      											array('class'=>'form-control', 
      														'placeholder'=>'Tytuł ksiazki', 
      														'onkeyup' => 'lookUpTitle(this.value)')
      										 );
        ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo Form::label('Autor', 'author', array('class'=>'col-sm-2 control-label')); ?>
      <div class="col-sm-4">
      <?php echo Form::input('author', Input::get('author',''),array('class'=>'form-control', 'placeholder'=>'Autor ksiazki')); ?>
      </div>
    </div>
		<div class="form-group">
			
			<div class='col-sm-offset-2 col-sm-4'>
				<?php echo Form::submit('search', 'Szukaj', array('class' => 'btn btn-primary')); ?>
      </div>
		</div>
    
  </fieldset>

<?php echo Form::close(); ?>
<br>
<div id="ac_div"></div>
