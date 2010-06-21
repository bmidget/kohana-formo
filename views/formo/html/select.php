<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?php echo $class; ?>>
	<label><?php echo ucfirst($field->label()); ?>:</label>
	<?php if ($field->get('editable') === TRUE): ?>
		<?php echo $field->render('html'); ?>
	<?php else: ?>
		<span><?php echo $field->val(); ?></span>
	<?php endif; ?>
	<span class="error-message"><?php echo ucfirst($field->error()); ?></span>
</p>
