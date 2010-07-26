<?php echo $open; ?>
	<label>
		<?php echo $label; ?>
		<?php echo $message; ?>
		<span class="field">
			<?php foreach ($field->fields() as $radio): ?>
				<?php echo $radio->render('html'); ?>
			<?php endforeach; ?>
		</span>
	</label>
<?php echo $close; ?>