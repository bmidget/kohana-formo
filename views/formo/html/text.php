<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?php echo $class; ?>>
	<label><?php echo UTF8::ucfirst($field->label()); ?>:</label>
	<?php if ($field->get('editable') === TRUE): ?>
		<?php echo $field->add_class('input'); ?>
	<?php else: ?>
		<span><?php echo $field->val(); ?></span>
	<?php endif; ?>
	<span class="error-message"><?php echo UTF8::ucfirst($field->error()); ?></span>
</p>