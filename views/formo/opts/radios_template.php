<span class="radio opts">
<?php foreach ($opts as $key => $opt): ?>
	<label>
		<span class="radio opt"><input type="radio" name="<?=$field->alias()?>" value="<?=$key?>" /></span>
		<span class="radio label"><?=$opt?></span>
	</label>
<?php endforeach; ?>
</span>