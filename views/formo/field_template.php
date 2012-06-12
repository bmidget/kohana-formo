<div class="field <?=$field->get('driver')?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?=$field->alias()?>">
	<?php if ($label): ?>
	<label>
		<span class="label"><?=$field->label()?></span>
		<span class="field">
			<?=$field->open().$field->render_opts().$field->close()?>
		</span>
		<?php if ($error): ?>
		<span class="error-message"><?=$error?></span>
		<?php endif; ?>
	</label>
	<?php else: ?>
		<?php if ($title): ?>
		<span class="title"><?=$title?></span>
		<?php endif; ?>
		<span class="field">
			<?=$field->open().$field->render_opts().$field->close()?>
		</span>
		<?php if ($error): ?>
		<span class="error-message"><?=$error?></span>
		<?php endif; ?>
	<?php endif; ?>
</div>