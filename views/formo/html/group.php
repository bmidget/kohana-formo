<div class="group">
	<h2><?php echo str_replace('_', ' ', $field->alias()); ?></h2>
	<?php if ($error = $field->error() AND $error !== TRUE): ?>
		<span class="error-message"><?php echo $field->error(); ?></span>
	<?php endif; ?>
	<?php foreach ($field->fields() as $_field): ?>
		<?php echo $_field->render(); ?>
	<?php endforeach; ?>
</div>