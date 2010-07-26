<?php echo $open; ?>
	<label>
		<?php echo $label; ?>
		<?php echo $messagel ?>
		<span class="field">
			<?php foreach ($field->fields() as $checkbox): ?>
				<?php echo $checkbox->render('html'); ?>
			<?php endforeach; ?>
		</span>
	</label>
<?php echo $close; ?>