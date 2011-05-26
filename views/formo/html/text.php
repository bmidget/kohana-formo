<?php echo $open; ?>
	<?php echo $label; ?>
	<?php if ($field->get('editable') === TRUE): ?>
		<?php echo $field->render(); ?>
	<?php else: ?>
		<span id="<?php echo $field->name(); ?>" class="field"><?php echo $field->val(); ?></span>
	<?php endif; ?>
	<?php if($field->error()) echo $message; ?>
<?php echo $close; ?>
