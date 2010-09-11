<?php echo $open; ?>
	<label>
		<span class="input"><?php echo $field->render(); ?></span>
		<span class="label"><?php echo UTF8::ucfirst($field->label()); ?></span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>