<span class="checkbox opts">
<?php foreach ($opts as $key => $opt): ?>
	<label>
		<span class="checkbox opt"><input type="checkbox" name="<?=$field->alias()?>[]" value="<?=$key?>" /></span>
		<span class="checkbox label"><?=$opt?></span>
	</label>
<?php endforeach; ?>
</span>