<?php if ($field->get('blank') === TRUE): ?>
<option></option>
<?php endif; ?>
<?php foreach ($opts as $key => $opt): ?>
	<?php $key = (string) $key; ?>
	<?php if (is_array($opt)): ?>
		<optgroup label="<?php echo $key; ?>">
		<?php foreach ($opt as $_key => $_opt): ?>
			<?php $_key = (string) $_key; ?>
			<?php if ($field->val() == $_key OR (is_array($field->val()) AND in_array($_key, $field->val()))): ?>
			<option value="<?php echo $_key; ?>" selected="selected"><?php echo $_opt; ?></option>
			<?php else: ?>
			<option value="<?php echo $_key; ?>"><?php echo $_opt; ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</optgroup>
	<?php else: ?>
		<?php if ($field->val() == $key OR (is_array($field->val()) AND in_array($key, $field->val()))): ?>
		<option value="<?php echo $key; ?>" selected="selected"><?php echo $opt; ?></option>
		<?php else: ?>
		<option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>