<?php echo $open; ?>
	<label<?php if ($id = $view->attr('id')) echo ' for="'.$id.'"'; ?>>
		<?php echo $label; ?>
		<span class="field">
			<?php if ($field->get('editable') === TRUE): ?>
				<?php echo $view->open(); ?>
					<option value=""></option>
					<?php foreach ($field->fields() as $option): ?>
						<?php echo $option->render()?>
					<?php endforeach; ?>
				<?php echo $view->close(); ?>
			<?php else: ?>
				<span><?php echo $field->val(); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>
