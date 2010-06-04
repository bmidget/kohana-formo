<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?=$class?>>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
	<?php if ($field->get('editable') === TRUE): ?>
		<?=$field->render('html')?>
	<?php else: ?>
		<span><?=$field->val()?></span>
	<?php endif; ?>
	<span class="errorMessage"><?=ucfirst($field->error())?></span>
</p>
