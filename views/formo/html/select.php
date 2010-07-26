<?php echo $open; ?>
	<label>
		<?php echo $label; ?>
		<span class="field">
			<?php if ($field->get('editable') === TRUE): ?>
				<?php echo $field->render('html'); ?>
			<?php else: ?>
				<span><?php echo $field->val(); ?></span>
			<?php endif; ?>
		</span>
	<?php echo $message; ?>
<?php echo $close; ?>
