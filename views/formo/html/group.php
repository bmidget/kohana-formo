<div>
	<h2><?=ucfirst($form->alias())?></h2>
	<?php if ($form->error): ?>
		<div>There were errors with this part</div>
	<?php endif; ?>
	<?php foreach ($form->fields() as $field): ?>
		<div><?=$field->render('html')?></div>
	<?php endforeach; ?>
</div>