<span class="checkbox opts">
<?php foreach ($opts as $key => $opt): ?>
	<label class="checkbox">
		<input type="checkbox" name="<?php echo $field->name(); ?>[]" value="<?php echo $key; ?>" <?php if (in_array($key, explode(',', $field->val()))) echo ' checked="checked"'; ?> /> <span class="opt-label"><?php echo $opt; ?></span>
	</label>
<?php endforeach; ?>
</span>