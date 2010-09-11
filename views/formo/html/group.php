<div class="group">
	<h2><?php echo UTF8::ucfirst($group->alias()); ?></h2>
	<?php if ($group->error): ?>
		<div>There were errors with this section</div>
	<?php endif; ?>
	<?php foreach ($group->fields() as $group): ?>
		<?php echo $group->generate(); ?>
	<?php endforeach; ?>
</div>