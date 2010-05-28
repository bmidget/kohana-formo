<p>
	<label><?=$field?><?=$field->label()->text(array('callback' => 'ucfirst'))->text()?></label>
	<span class="error-message"><?=ucfirst($field->error())?></span>
</p>