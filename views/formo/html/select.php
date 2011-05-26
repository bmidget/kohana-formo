<?php echo $open; ?>
	<?php echo $label; ?>
	<?php if ($field->get('editable') === TRUE): ?>
		<?php echo $field->open(); ?>
			<option value=""></option>
			<?php foreach ($field->fields() as $option): ?>
				<?php echo $option->generate()?>
			<?php endforeach; ?>
		<?php echo $field->close(); ?>
	<?php else: ?>
		<span id="<?php echo $field->name(); ?>" class="field"><?php echo $field->val(); ?></span>
	<?php endif; ?>
	<?php if($field->error()) echo $message; ?>
<?php echo $close; ?>
