<?=$field->label()->text(array('callback' => 'ucfirst', '.=' => ':'))?>
<?=$field->add_class('input')->attr('rows', 10)?>
<span class="error-message"><?=ucfirst($field->error())?></span>