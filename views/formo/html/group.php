<fieldset name="<?php echo $group->alias(); ?>">
	<legend><?php echo UTF8::ucfirst(str_replace('_', ' ', $group->alias())); ?></legend>
	<?php if ($group->error): ?>
		<div class="error-message">There were errors with this section</div>
	<?php endif; ?>
	<?php foreach ($group->fields() as $group): ?>
		<?php echo $group->generate(); ?>
	<?php endforeach; ?>
</fieldset>