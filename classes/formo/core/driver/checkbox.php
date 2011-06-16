<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Checkbox_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Checkbox extends Formo_Driver {

	protected $view = 'checkbox';
	public $empty_input = TRUE;

	public function checked()
	{
		$parent_newval = $this->field->parent()->get('new_value');
		$parent_value = $this->field->parent()->get('value');

		if (Formo::is_set($parent_newval) === FALSE AND ! $this->field->parent(Formo::PARENT)->sent())
			return in_array($this->val(), (array) $parent_value);

		return (in_array($this->field->val(), (array) $parent_newval));
	}

	// Setup the html field
	public function html()
	{
		$this->view()
			->set_var('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->field->parent()->name().'[]')
			->attr('value', $this->field->val());

		if ($this->field->checked())
		{
			$this->view()->attr('checked', 'checked');
		}
	}

}
