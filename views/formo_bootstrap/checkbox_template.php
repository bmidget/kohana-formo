<div class="field control-group <?=$field->get('driver')?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?=$field->alias()?>">
	<label><?=$field->open().$field->render_opts().$field->close()?> <?=$field->label()?></label>

	<?php if ($msg = $field->error()): ?>
		<span class="help-block"><?=$msg?></span>
	<?php elseif ($msg = $field->get('message')): ?>
		<span class="help-block"><?=$msg?></span>
	<?php endif; ?>
</div>