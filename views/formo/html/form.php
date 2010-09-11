<?php if ($error = $form->error() AND $error !== TRUE): ?>
	<span class="error-message"><?php echo UTF8::ucfirst($form->error()); ?></span>
<?php endif; ?>

<?php echo $form->open(); ?>
	<?php foreach ($form->fields() as $field): ?>
		<?php echo $field->generate(); ?>
	<?php endforeach; ?>
<?php echo $form->close(); ?>