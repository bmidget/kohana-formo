<?php echo $open; ?>
	<label>
		<?php echo $label; ?>
		<span class="field">
			<?php if ($field->get('editable') === TRUE): ?>
				<?php echo $field->open(); ?>
					<option value=""></option>
					<?php foreach ($field->fields() as $option): ?>
						<?php echo $option->generate()?>
					<?php endforeach; ?>
				<?php echo $field->close(); ?>
			<?php else: ?>
				<span><?php echo $field->val(); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>
