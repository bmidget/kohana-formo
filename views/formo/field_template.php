<div class="field <?php echo $field->get('driver'); ?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?php echo $field->alias(); ?>">
	<?php if ($label): ?>
	<label>
		<span class="label"><?php echo $field->label(); ?></span>
		<?php if ($field->get('editable')): ?>
		<span class="field">
			<?php echo $field->open().$field->render_opts().$field->close(); ?>
		</span>
		<?php else: ?>
		<span class="field uneditable"><?php echo $field->val(); ?></span>
		<?php endif; ?>
		<?php if ($error): ?>
		<span class="error-message"><?php echo $error; ?></span>
		<?php endif; ?>
	</label>
	<?php else: ?>
		<?php if ($title): ?>
		<span class="title"><?php echo $title; ?></span>
		<?php endif; ?>
		<?php if ($field->get('editable')): ?>
		<span class="field">
			<?php echo $field->open().$field->render_opts().$field->close(); ?>
		</span>
		<?php else: ?>
		<span class="field uneditable"><?php echo $field->val(); ?></span>
		<?php endif; ?>
		<?php if ($error): ?>
		<span class="error-message"><?php echo $error; ?></span>
		<?php endif; ?>
	<?php endif; ?>
</div>