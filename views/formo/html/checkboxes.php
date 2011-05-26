<fieldset name="<?php echo $field->name(); ?>">
	<legend><?php echo UTF8::ucfirst(str_replace('_', ' ', $field->label())); ?></legend>
	<?php if($field->error()) echo $message; ?>
	<?php foreach ($field->fields() as $checkbox): ?>
		<?php echo $checkbox->generate(); ?>
	<?php endforeach; ?>
</fieldset>