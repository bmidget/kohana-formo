<?php echo $open; ?>
	<label<?php if ($id = $field->attr('id')) echo ' for="'.$id.'"'; ?>>
		<span class="input"><?php echo $field->html(); ?></span>
		<span class="label"><?php echo UTF8::ucfirst($field->label()); ?></span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>