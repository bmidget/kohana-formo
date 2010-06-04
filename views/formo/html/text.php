<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?=$class?>>
	<?=$field->label()->text(array('callback' => 'ucfirst', '.=' => ':'))?>
	<?php if ($field->get('editable') === TRUE): ?>
		<?=$field->add_class('input')?>
	<?php else: ?>
		<span><?=$field->val()?></span>
	<?php endif; ?>
	<span class="error-message"><?=ucfirst($field->error())?></span>
</p>