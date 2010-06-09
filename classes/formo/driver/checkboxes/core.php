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
	
	public function check(array $aliases)
	{
		$new_value = (array) $this->field->get('value');
		foreach ($aliases as $alias)
		{
			$value = $this->field->options[$alias]['value'];
			if ( ! in_array($value, $new_value))
			{
				$new_value[] = $value;
			}
		}
		
		$this->field->set('value', $new_value);
	}

	public function uncheck(array $aliases)
	{
		$new_value = (array) $this->field->get('value');
		foreach ($aliases as $alias)
		{
			$value = $this->field->options[$alias]['value'];
			unset($new_value[array_search($value, $new_value)]);
		}
		
		$this->field->set('value', $new_value);
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