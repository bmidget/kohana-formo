<span class="radio opts">
<?php foreach ($opts as $key => $opt): ?>
	<label class="radio">
		<input type="radio" name="<?php echo $field->name(); ?>" value="<?php echo $key; ?>" <?php if ($key == $field->val()) echo ' checked="checked"'; ?> /> <span class="opt-label"><?php echo $opt; ?></span>
	</label>
<?php endforeach; ?>
</span>