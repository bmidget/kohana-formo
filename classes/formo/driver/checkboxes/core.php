<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkboxes_Core extends Formo_Driver {

	protected $view = 'checkboxes';
	
	public function load($values)
	{
		$this->val($values);
		
		if ( ! is_array($values))
		{
			$values = array($values);
		}		
	}
	
	// Make checkboxes checked
	public function check(array $aliases)
	{
		foreach ($aliases as $alias)
		{
			// You can't check an option that doesn't exist
			if (empty($this->field->options[$alias]))
				continue;
			
			// Mark the option as checked
			$this->field->options[$alias]['checked'] = TRUE;
		}		
	}
	
	// Uncheck checkboxes
	public function uncheck(array $aliases)
	{
		foreach ($aliases as $alias)
		{
			// You can't check an option that doesn't exist
			if (empty($this->field->options[$alias]))
				continue;
				
			// Mark the option as not checked
			$this->field->options[$alias]['checked'] = FALSE;
		}
	}

	public function html()
	{
		foreach ($this->render_field->options as $alias => $options)
		{
			$checkbox = Formo_Field::factory($alias, 'checkbox', $options);
			
			$this->render_field->append($checkbox);
		}
	}

}