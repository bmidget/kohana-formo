<p>
	<?=$field->label()->text(array('callback' => 'ucfirst', '.=' => ':'))?>
	<?=$field->add_class('input')?>
	<span class="error-message"><?=ucfirst($field->error())?></span>
</p>