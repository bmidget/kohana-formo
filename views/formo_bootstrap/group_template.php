<div class="formo-<?php echo $field->get('driver'); ?>" id="form-container-<?php echo $field->alias(); ?>" <?php if (($key = $field->get('blueprint_key')) !== NULL) echo ' data-blueprintKey="'.$key.'"'; ?>>
	<?php echo $field->open(); ?>
		<?php foreach ($field->as_array() as $_field): ?>
		<?php echo $_field->render(); ?>
		<?php endforeach; ?>
	<?php echo $field->close(); ?>
</div>