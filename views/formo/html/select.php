<?php echo $open; ?>
	<label<?php if ($id = $this->attr('id')) echo ' for="'.$id.'"'; ?>>
		<?php echo $label; ?>
		<span class="field">
			<?php if ($this->editable() === TRUE): ?>
				<?php echo $this->open(); ?>
					<option value=""></option>
					<?php foreach ($this->fields() as $option): ?>
						<?php echo $option->render()?>
					<?php endforeach; ?>
				<?php echo $this->close(); ?>
			<?php else: ?>
				<span><?php echo $this->val(); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>
