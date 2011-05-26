<div class="group">
	<h2><?php echo UTF8::ucfirst(str_replace('_', ' ', $group->alias())); ?></h2>
	<?php if ($error = $group->error() AND $error !== TRUE): ?>
		<span class="error-message"><?php echo UTF8::ucfirst($group->error()); ?></span>
	<?php endif; ?>
	<?php foreach ($group->fields() as $group): ?>
		<?php echo $group->generate(); ?>
	<?php endforeach; ?>
</div>