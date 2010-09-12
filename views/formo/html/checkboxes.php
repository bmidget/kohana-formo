<?php echo $open; ?>
	<label>
		<?php echo $label; ?>
		<?php echo $message; ?>
		<span class="field">
			<?php foreach ($field->fields() as $checkbox): ?>
				<?php echo $checkbox->generate(); ?>
			<?php endforeach; ?>
		</span>
	</label>
<?php echo $close; ?>