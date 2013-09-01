<div class="formo-<?=$field->get('driver')?>" id="form-container-<?=$field->alias()?>" <?php if (($key = $field->get('blueprint_key')) !== NULL) echo ' data-blueprint-key="'.$key.'"'; ?>>
	<?=$field->open()?>
		<?php foreach ($field->as_array() as $_field): ?>
		<?=$_field->render()?>
		<?php endforeach; ?>
	<?=$field->close()?>
</div>