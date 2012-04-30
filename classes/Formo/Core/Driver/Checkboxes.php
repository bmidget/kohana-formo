<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Checkboxes_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Checkboxes extends Formo_Driver {

	protected $_view_file = 'checkboxes';
	public $empty_input = TRUE;

	public function load($values)
	{
		// If this field is not being rendered, do nothing
		if ($this->_field->get('render') === FALSE)
			return;

		$this->val($values);

		if ( ! is_array($values))
		{
			$values = array($values);
		}
	}

	protected function _get_val()
	{
		$new_value = $this->_field->get('new_value');

		// If the form was sent but the field wasn't set, return empty array as value
		if ($this->_field->sent() AND Formo::is_set($new_value) === FALSE)
			return array();

		// Otherwise return the value that's set
		return (Formo::is_set($new_value, $new_value) === TRUE)
			? (array) $new_value
			: (array) $this->_field->get('value');
	}

	public function not_empty()
	{
		$value = $this->val();
		// If the value is empty, it doesn't pass
		return empty($value) === FALSE;
	}

	public function check(array $aliases)
	{
		$new_value = (array) $this->_field->get('value');
		foreach ($aliases as $alias)
		{
			$options = $this->_field->get('options');
			$value = $options[$alias]['value'];

			if ( ! in_array($value, $new_value))
			{
				$new_value[] = $value;
			}
		}

		$this->_field->set_var('value', $new_value);
	}

	public function uncheck(array $aliases)
	{
		$new_value = (array) $this->_field->get('value');
		foreach ($aliases as $alias)
		{
			$options = $this->_field->get('options');
			$value = $options[$alias]['value'];

			unset($new_value[array_search($value, $new_value)]);
		}

		$this->_field->set_var('value', $new_value);
	}

	public function option_name()
	{
		return $this->_field->name().'[]';
	}

	public function html()
	{
	}

}
