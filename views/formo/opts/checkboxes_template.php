<span class="checkbox opts">
<?php foreach ($opts as $key => $opt): ?>
	<label>
		<?php if (in_array($key, $field->val())): ?>
		<span class="checkbox opt"><input type="checkbox" name="<?php echo $field->name(); ?>[]" value="<?php echo $key; ?>" checked="checked" /></span>
		<?php else: ?>
		<span class="checkbox opt"><input type="checkbox" name="<?php echo $field->name(); ?>[]" value="<?php echo $key; ?>" /></span>
		<?php endif; ?>
		<span class="checkbox label"><?php echo $opt; ?></span>
	</label>
<?php endforeach; ?>
</span>