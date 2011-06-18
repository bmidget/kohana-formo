<?php echo $open; ?>
	<?php echo $label; ?>
	<?php echo $message; ?>
	<span class="field">
		<?php foreach ($this->fields() as $checkbox): ?>
			<?php echo $checkbox->render(); ?>
		<?php endforeach; ?>
	</span>
<?php echo $close; ?>