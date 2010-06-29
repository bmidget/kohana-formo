<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkboxes_Core extends Formo_Driver {

	protected $view = 'checkboxes';
	public $empty_input = TRUE;
	
	public function load($values)
	{
		$this->val($values);
		
		if ( ! is_array($values))
		{
			$values = array($values);
		}		
	}

	public function getval()
	{
		// If the form was sent but the field wasn't set, return empty array as value
		if ($this->field->sent() AND Formo::notset($this->field->get('new_value')))
			return array();
		
		// Otherwise return the value that's set
		return ( ! Formo::notset($this->field->get('new_value')))
			? (array) $this->field->get('new_value')
			: (array) $this->field->get('value');
	}

	public function not_empty()
	{
		$value = $this->val();
		// If the value is empty, it doesn't pass
		return empty($value) === FALSE;
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
			$this->render_field->append(Formo::field($alias, 'checkbox', $options));
		}
	}

}