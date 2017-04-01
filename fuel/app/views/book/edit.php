<?php

use Fuel\Core\Form;

if (isset($submit_url))
	echo Form::open(array("class"=>"form-horizontal", "action" => $submit_url));
else
	echo Form::open(array("class"=>"form-horizontal"));

$type_choice = array(
  'x' => 'Nieznany',
  'a' => 'Dorośli',
  'd' => 'Dzieci',
  'r' => 'Religia',
  'f' => 'Film'
);

?>

  <fieldset>
    <div class="form-group <?php if (isset($error['title'])) echo "has-error";?>">
      <?php echo Form::label('Tytuł', 'title', array('class'=>'col-sm-2 control-label')); ?>
      
      <div class="col-sm-4">
      	<?php echo Form::input('title', htmlspecialchars($title_in),array('class'=>'form-control', 'placeholder'=>'Tytuł ksiażki')); ?>
      </div>
    </div>
    
    <?php
    $one_author = count($authors_in) == 1;
    
    for ($aid = 0; $aid < count($authors_in); $aid++) {
    		$alabel = $one_author ? 'Autor' : 'Autor '.($aid + 1);
    		$anamed_id = 'author_'.$authors_in[$aid]['id'];
    		$adel_id = 'delauthor_'.$authors_in[$aid]['id'];
    		
    ?>
    
	    <div class="form-group	<?php if (isset($error[$anamed_id])) echo "has-error";?>">
	      <?php echo Form::label($alabel, $anamed_id, array('class'=>'col-sm-2 control-label')); ?>
	      <div class="col-sm-4">
	      	<?php echo Form::input($anamed_id, $authors_in[$aid]['name'], 
	      			array('class'=>'form-control', 
	      						'placeholder'=>'Autor książki',
	      					  'readonly' => !($authors_in[$aid]['active']),
	      						'onkeyup' => 'lookUp(this, \'authors\')'
	      	))
	      	 ?>
	      </div>
	      <div class="row">
	      	<?php 
	      	if ($one_author == false)
	      		echo Form::submit($adel_id, 'usun autora', array('class' => 'btn btn-default'));
	      	
	      	if (($aid + 1) == count($authors_in))
		      	echo Form::submit('add_author', 'dodaj autora', array('class' => 'btn btn-default'));
	      	?>
	      	
	      </div>
	    </div>
	  <?php } ?>

    <div class="form-group <?php if (isset($error['tag'])) echo "has-error";?>">
      <?php echo Form::label('Identifikator', 'tag', array('class'=>'col-sm-2 control-label')); ?>
      <div class="col-sm-4">
      	<?php echo Form::input('tag', $tag_in, 
      			array('class'=>'form-control', 
      						'placeholder'=>'Identifikator',
      	)); ?>
      </div>
    </div>
    <div class="form-group">
      <?php echo Form::label('Rodzaj', 'type', array('class'=>'col-sm-2 control-label')); ?>
      <div class="col-sm-4">
      	<?php echo Form::select('type', $type_in, $type_choice, 
      			array('class'=>'form-control',
      						'onchange' => "getLastTag()"
      	)); ?>
      </div>
    </div>
    <div class="form-group">
    	<?php echo Form::label('Ostatni identyfikator', 'last_tag_label', array('class'=>'col-sm-2  control-label')) ?>
    	<div class="col-sm-4">
    		<?php echo Form::input('last_tag', '', array('class' => 'form-control', 'readonly' => 'readonly'))?>
    	</div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-4">
        <?php echo Form::submit('save_book', 'Zapisz', array('class' => 'btn btn-default')); ?>
        <?php if (isset($return_url)) echo Html::anchor($return_url, 'Wstecz',array('class' => 'btn btn-default')); ?>
      </div>
    </div>
    
  </fieldset>

<?php echo Form::close(); ?>
