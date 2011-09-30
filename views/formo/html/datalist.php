<?php echo $open; ?>
	<label<?php if ($id = $this->attr('id')) echo ' for="'.$id.'"'; ?>>
		<?php echo $label; ?>
		<span class="field">
			<input list="<?=$this->attr('id')?>" name="<?=$this->_field->name()?>" value="<?=$this->_field->val()?>" />
		</span>
		<span class="datalist">
				<?php if ($this->editable() === TRUE): ?>
					<?php echo $this->open(); ?>
						<?php foreach ($this->fields() as $option): ?>
							<?php echo $option->render()?>
						<?php endforeach; ?>
					<?php echo $this->close(); ?>
				<?php else: ?>
					<span><?php echo $this->val(); ?></span>
				<?php endif; ?>
		</span>
	</label>
	<?php echo $message; ?>
<?php echo $close; ?>
