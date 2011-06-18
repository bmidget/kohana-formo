<?php echo $open; ?>
	<?php echo $label; ?>
	<?php echo $message; ?>
	<span class="field">
		<?php foreach ($this->fields() as $radio): ?>
			<?php echo $radio->render(); ?>
		<?php endforeach; ?>
	</span>
<?php echo $close; ?>