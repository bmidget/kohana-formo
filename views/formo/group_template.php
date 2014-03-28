<div class="group <?php echo $field->get('driver'); ?>" id="form-container-<?php echo $field->alias(); ?>">
	<?php echo $field->open(); ?>
		<?php foreach ($field->as_array() as $_field): ?>
		<?php echo $_field->render(); ?>
		<?php endforeach; ?>
	<?php echo $field->close(); ?>
</div>