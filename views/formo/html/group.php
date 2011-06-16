<div class="group">
	<h2><?php echo UTF8::ucfirst(str_replace('_', ' ', $field->alias())); ?></h2>
	<?php if ($error = $field->error() AND $error !== TRUE): ?>
		<span class="error-message"><?php echo $field->error(); ?></span>
	<?php endif; ?>
	<?php foreach ($field->fields() as $field): ?>
		<?php echo $view->render(); ?>
	<?php endforeach; ?>
</div>