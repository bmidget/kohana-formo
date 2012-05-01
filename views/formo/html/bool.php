<?php echo $open; ?>
	<label<?php if ($id = $this->attr('id')) echo ' for="'.$id.'"'; ?>>
		<span class="input"><?php echo $this->html(); ?></span>
		<span class="label"><?php echo $this->label(); ?></span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>