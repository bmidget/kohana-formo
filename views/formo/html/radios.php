<?php echo $open; ?>
	<?php echo $label; ?>
	<?php echo $message; ?>
	<span class="field">
		<?php foreach ($field->fields() as $radio): ?>
			<?php echo $radio->generate(); ?>
		<?php endforeach; ?>
	</span>
<?php echo $close; ?>