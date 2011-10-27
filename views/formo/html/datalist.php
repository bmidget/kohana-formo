<?php echo $open; ?>
	<label<?php if ($id = $this->attr('id')) echo ' for="'.$id.'"'; ?>>
		<?php echo $label; ?>
		<span class="field">
			<input type="text" list="<?php echo $this->attr('id'); ?>" name="<?php echo $this->_field->name(); ?>" value="<?php echo $this->_field->val(); ?>" />
		</span>
		<span class="datalist">
			<?php if ($this->editable() === TRUE): ?>
				<?php echo $this->open(); ?>
					<?php foreach ($this->_field->get('options') as $value): ?>
						<option<?php echo HTML::attributes($this->get_option_attr('select', $value, $value)); ?>>
					<?php endforeach; ?>
				<?php echo $this->close(); ?>
			<?php else: ?>
				<span><?php echo $this->val(); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>
