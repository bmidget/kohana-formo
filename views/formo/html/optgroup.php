<optgroup label="<?=$this->attr('label')?>">
	<?php foreach ($this->fields() as $option): ?>
		<?php echo $option->render()?>
	<?php endforeach; ?>
</optgroup>