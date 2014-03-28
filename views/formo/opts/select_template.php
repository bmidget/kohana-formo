<?php if ($field->get('blank') === TRUE): ?>
<option></option>
<?php endif; ?>
<?php foreach ($opts as $key => $opt): ?>
	<?php if ($field->val() == (string) $key): ?>
	<option value="<?php echo $key; ?>" selected="selected"><?php echo $opt; ?></option>
	<?php else: ?>
	<option value="<?php echo $key; ?>"><?php echo $opt; ?></option>
	<?php endif; ?>
<?php endforeach; ?>