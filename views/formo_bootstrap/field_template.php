<div class="field form-group formo-<?php echo $field->get('driver'); ?><?php if ($error = $field->error()) echo ' has-error'; ?>" id="field-container-<?php echo $field->alias(); ?>">
	<?php if ($title): ?>
		<span class="title"><?php echo $title; ?></span>
	<?php elseif ($label = $field->label()): ?>
		<label for="<?php echo $field->attr('id'); ?>"><?php echo $label; ?></label>
	<?php endif; ?>

	<?php echo $field->open().$field->html().$field->render_opts().$field->close(); ?>

	<?php if ($msg = $field->error()): ?>
		<span class="help-block"><?php echo $msg; ?></span>
	<?php elseif ($msg = $field->get('message')): ?>
		<span class="help-block"><?php echo $msg; ?></span>
	<?php endif; ?>
</div>