<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?php echo $class; ?>>
	<label><?php echo UTF8::ucfirst($field->label()); ?>:</label>
	<span class="error-message"><?php echo UTF8::ucfirst($field->error()); ?></span>
	<?php foreach ($field->fields() as $radio): ?>
		<span>
			<?php echo $radio->render('html'); ?>
		</span>
	<?php endforeach; ?>
</p>