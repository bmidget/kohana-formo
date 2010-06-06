<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?=$class?>>
	<label><?=ucfirst($field->label())?>:</label>
	<span class="error-message"><?=ucfirst($field->error())?></span>
	<?php foreach ($field->fields() as $radio): ?>
		<span>
			<?=$radio->render('html')?>
		</span>
	<?php endforeach; ?>
</p>