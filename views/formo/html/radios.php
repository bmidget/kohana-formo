<?php echo $open; ?>
	<?php echo $label; ?>
	<?php echo $message; ?>
	<span class="field">
		<?php foreach ($this->get('options') as $key => $option): ?>
			<span class="radio">
				<label>
					<span class="input"><input<?php echo HTML::attributes($this->get_option_attr('radio', $option, $key))?> /></span>
					<span class="text"><?php echo $this->option_label($option, $key)?></span>
				</label>
			</span>
		<?php endforeach; ?>
	</span>
<?php echo $close; ?>