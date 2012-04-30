<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Input extends Formo_Driver {

	protected $_view_file = 'input';
	
	protected function get_type()
	{
		return ($type = $this->_field->get('type'))
			? $type
			: 'text';
	}
	
	public function pre_validate()
	{
		parent::pre_validate();
		$this->_add_input_rules();
	}
	
	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', $this->get_type())
			->attr('name', $this->name())
			->attr('value', $this->_field->val());
	}
	
	protected function _add_input_rules()
	{
		// Grab the rules from the formo config
		$rules = Formo::config($this->_field, 'input_rules.'.$this->get_type());

		if ($rules)
		{
			// Attach rules to the field's parent
			$this->_field->parent()->rules($this->_field->alias(), $rules);
		}
		
		if ($bindings = Formo::config($this->_field, 'formo.html5_bindings.'.$this->get_type()))
		{
			$this->_field->set('bindings', $bindings);
		}
	}

}