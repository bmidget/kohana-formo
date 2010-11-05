<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Checkboxes_Core class.
 *
 * @package  Formo
 */
class Formo_Driver_Checkboxes_Core extends Formo_Driver {

	protected $view = 'checkboxes';
	public $empty_input = TRUE;

	public function load($values)
	{
		// If this field is not being rendered, do nothing
		if ($this->field->get('render') === FALSE)
			return;

		$this->val($values);

		if ( ! is_array($values))
		{
			$values = array($values);
		}
	}

	public function getval()
	{
		// If the form was sent but the field wasn't set, return empty array as value
		if ($this->field->sent() AND Formo::is_set($this->field->get('new_value')) === FALSE)
			return array();

		// Otherwise return the value that's set
		return (Formo::is_set($this->field->get('new_value'), $new_value) === TRUE)
			? (array) $new_value
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
			$options = $this->field->get('options');
			$value = $options[$alias]['value'];

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
			$options = $this->field->get('options');
			$value = $options[$alias]['value'];

			unset($new_value[array_search($value, $new_value)]);
		}

		$this->field->set('value', $new_value);
	}

	public function html()
	{
		foreach ($this->field->get('options') as $alias => $options)
		{
			$this->field->append(Formo::field($alias, 'checkbox', $options));
		}
	}

}
