<p>
	<?=$field->label()->text('callback', 'ucfirst')->text('.=', ':')?>
		<?=$field->render('html')?>
	<span class="errorMessage"><?=ucfirst($field->error())?></span>
</p>
