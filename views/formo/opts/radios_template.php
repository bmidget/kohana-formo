<span class="radio opts">
<?php foreach ($opts as $key => $opt): ?>
	<label>
		<?php if ($field->val() == $key): ?>
		<span class="radio opt"><input type="radio" name="<?php echo $field->name(); ?>" value="<?php echo $key; ?>" checked="checked"/></span>
		<?php else: ?>
		<span class="radio opt"><input type="radio" name="<?php echo $field->name(); ?>" value="<?php echo $key; ?>" /></span>
		<?php endif; ?>
		<span class="radio label"><?php echo $opt; ?></span>
	</label>
<?php endforeach; ?>
</span>