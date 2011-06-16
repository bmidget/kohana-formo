<?php echo $open; ?>
	<label<?php if ($id = $view->attr('id')) echo ' for="'.$id.'"'; ?>>
		<span class="input"><?php echo $view->html(); ?></span>
		<span class="label"><?php echo $view->label(); ?></span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>