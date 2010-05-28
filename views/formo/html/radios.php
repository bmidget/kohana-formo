<p>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
	<?php foreach ($field->fields() as $radio): ?>
		<span>
			<label style="display:inline;font-weight:normal">
				<span style="margin-right: 10px"><?=$radio?><?=$radio->label()->text('callback', 'ucfirst')->text()?></span>
			</label>
		</span>
	<?php endforeach; ?>
	<span class="errorMessage"><?=ucfirst($field->error)?></span>
</p>