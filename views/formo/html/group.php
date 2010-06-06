<div>
	<h2><?=ucfirst($group->alias())?></h2>
	<?php if ($group->error): ?>
		<div>There were errors with this part</div>
	<?php endif; ?>
	<?php foreach ($group->fields() as $group): ?>
		<div><?=$group->render('html')?></div>
	<?php endforeach; ?>
</div>