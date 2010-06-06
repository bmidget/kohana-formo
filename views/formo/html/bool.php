<?php $class = ($field->error()) ? ' class="error"' : NULL; ?>
<p<?=$class?>>
	<label>
		<span class="field"><?=$field?></span>
		<span class="label"><?=ucfirst($field->label())?></span>
	</label>
	<span class="error-message"><?=ucfirst($field->error())?></span>
</p>