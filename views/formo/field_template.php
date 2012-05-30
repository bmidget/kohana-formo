<div class="field <?=$field->get('driver')?>" id="field-container-<?=$field->alias()?>">
	<?php if ($label): ?>
	<label>
		<span class="label"><?=$field->label()?></span>
		<span class="field">
			<?=$field->open().$field->render_opts().$field->close()?>
		</span>
	</label>
	<?php else: ?>
		<?php if ($title): ?>
		<span class="title"><?=$title?></span>
		<?php endif; ?>
		<span class="field">
			<?=$field->open().$field->render_opts().$field->close()?>
		</span>
	<?php endif; ?>
</div>