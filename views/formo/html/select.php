<p>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
	<?=$field->add_class('input')?>
	<span class="errorMessage"><?=ucfirst($field->_error)?></span>
</p>
