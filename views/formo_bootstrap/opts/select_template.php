<?php if ($field->get('blank') === TRUE): ?>
<option></option>
<?php endif; ?>
<?php foreach ($opts as $key => $opt): ?>
	<?php if (is_array($opt)): ?>
		<optgroup label="<?=$key?>">
		<?php foreach ($opt as $_key => $_opt): ?>
			<?php if ($field->val() == $_key): ?>
			<option value="<?=$_key?>" selected="selected"><?=$_opt?></option>
			<?php else: ?>
			<option value="<?=$_key?>"><?=$_opt?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</optgroup>
	<?php else: ?>
		<?php if ($field->val() == $key): ?>
		<option value="<?=$key?>" selected="selected"><?=$opt?></option>
		<?php else: ?>
		<option value="<?=$key?>"><?=$opt?></option>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>