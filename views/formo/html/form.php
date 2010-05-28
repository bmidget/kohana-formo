<?php if ($error = $form->error() AND $error !== TRUE): ?>
<div class=""><?=ucfirst($form->error())?></div>
<?php endif; ?>

<?=$form->open()?>
	<?php foreach ($form->fields() as $field): ?>
		<div><?=$field->render('html')?></div>
	<?php endforeach; ?>
<?=$form->close()?>