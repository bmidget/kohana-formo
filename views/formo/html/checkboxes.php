<p>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
	<span class="error-message"><?=ucfirst($field->error())?></span>
	<?php foreach ($field->fields() as $checkbox): ?>
		<span>
			<?=$checkbox->render('html')?>
		</span>
	<?php endforeach; ?>
</p>