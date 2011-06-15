<span class="radio">
	<label<?php if ($id = $field->attr('id')) echo ' for="'.$id.'"'; ?>><?php echo $field->html(); ?><?php echo UTF8::ucfirst($field->label()); ?></label>
</span>