<p>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
	<?php foreach ($field->fields() as $checkbox): ?>
		<span>
			<?=$checkbox?>
		</span>
	<?php endforeach; ?>
	<span class="errorMessage"><?=ucfirst($field->get('error'))?></span>
</p>