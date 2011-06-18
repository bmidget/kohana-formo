<?php echo $open; ?>
	<label<?php if ($id = $this->attr('id')) echo ' for="'.$id.'"'; ?>>
		<?php echo $label; ?>
		<span class="field">
			<?php if ($this->editable() === TRUE): ?>
				<?php echo $this->add_class('input')->html(); ?>
			<?php else: ?>
				<span><?php echo $this->val(); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>