
		<div class="col-sm-offset-<?php echo isset($offset) ? $offset : 1?> col-sm-4">
		<table><tr>
		<?php
		foreach ($buttons as $button) {
			$bt = html_tag('a', array('href' => $button[0], 'class' => 'btn btn-default'), $button[1]);
			$td = html_tag('td', '', $bt);
	
			echo $td;
		}
		?>
		</tr></table>
		</div>
