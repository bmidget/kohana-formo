<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?=$class?>>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
	<span class="error-message"><?=ucfirst($field->error())?></span>
	<?php foreach ($field->fields() as $radio): ?>
		<span>
			<?=$radio->render('html')?>
		</span>
	<?php endforeach; ?>
</p>