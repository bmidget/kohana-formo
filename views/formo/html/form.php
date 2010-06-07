<?php if ($error = $form->error() AND $error !== TRUE): ?>
<span class="error-message"><?=ucfirst($form->error())?></span>
<?php endif; ?>

<?=$form->open()?>
	<?php foreach ($form->fields() as $field): ?>
		<div><?=$field->render('html')?></div>
	<?php endforeach; ?>
<?=$form->close()?>