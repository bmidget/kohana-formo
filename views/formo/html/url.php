<?php echo $open; ?>
	<label>
		<?php echo $label; ?>
		<span class="field">
			<?php if ($field->get('editable') === TRUE): ?>
				<?php echo $field->add_class('input')->render(); ?>
			<?php else: ?>
				<span><?php echo $field->val(); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>
