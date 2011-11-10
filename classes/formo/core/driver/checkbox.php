<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Checkbox_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Checkbox extends Formo_Driver {

	protected $_view_file = 'checkbox';
	public $empty_input = TRUE;

	public function checked()
	{
		$parent_newval = $this->_field->parent()->get('new_value');
		$parent_value = $this->_field->parent()->get('value');

		if (Formo::is_set($parent_newval) === FALSE AND ! $this->_field->parent(Formo::PARENT)->sent())
			return in_array($this->val(), (array) $parent_value);

		return (in_array($this->_field->val(), (array) $parent_newval));
	}

	// Setup the html field
	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->_field->name().'[]')
			->attr('value', $this->_field->val());

		if ($this->_field->checked())
		{
			$this->_view->attr('checked', 'checked');
		}
	}

}
