<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?php echo $class; ?>>
	<label>
		<span class="field"><?php echo $field; ?></span>
		<span class="label"><?php echo ucfirst($field->label()); ?></span>
	</label>
	<span class="error-message"><?php echo ucfirst($field->error()); ?></span>
</p>