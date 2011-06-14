<?php echo $open; ?>
	<?php echo $label; ?>
	<?php echo $message; ?>
	<span class="field">
		<?php foreach ($field->fields() as $checkbox): ?>
			<?php echo $checkbox->render(); ?>
		<?php endforeach; ?>
	</span>
<?php echo $close; ?>