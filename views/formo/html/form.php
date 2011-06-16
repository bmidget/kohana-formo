<?php echo $view->open(); ?>
	<?php if ($error = $field->error() AND $error !== TRUE): ?>
		<span class="error-message"><?php echo UTF8::ucfirst($field->error()); ?></span>
	<?php endif; ?>
	<?php foreach ($field->fields() as $_field): ?>
		<?php echo $_field->render(); ?>
	<?php endforeach; ?>
<?php echo $view->close(); ?>