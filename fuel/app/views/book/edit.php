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

    <div id="form_authors">
			
			<?php
			for ($aid = 0; $aid < count($authors_in); $aid++) {
					$input_id = 'author_'.$authors_in[$aid]['id'];
					$field_id = 'form_author_'.$authors_in[$aid]['id'];
					$name = $authors_in[$aid]['name']
			?>
			
			<div id="<?php echo $field_id?>" class="form-group	<?php if (isset($error[$input_id])) echo "has-error";?>">
				<?php echo Form::label("Autor", null, array('class'=>'col-sm-2 control-label')); ?>
				<div class="col-sm-4">
					<?php echo Form::input($input_id, $name, 
							array('id' => $input_id,
										'class'=>'form-control', 
										'placeholder'=>'Autor książki',
										'onkeyup' => 'lookUp(this, \'authors\')'
					))
					?>
				</div>
				<label class="btn btn-default" onclick="deleteAuthorField('<?php echo $field_id?>')">Usuń</label>
			</div>
			<?php } ?>
		</div>
		
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-4">
				<label class="btn btn-default" onclick="createAuthorField()">Dodaj autora</label>
			</div>
		</div>

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
<?php echo Asset::js('book.js'); ?>