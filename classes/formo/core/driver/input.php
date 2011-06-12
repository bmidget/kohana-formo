<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Input extends Formo_Driver {

	protected $view = 'text';
	
	protected function get_type()
	{
		return ($type = $this->field->get('type'))
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
		$this->decorator
			->set('tag', 'input')
			->attr('type', $this->get_type())
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}
	
	protected function _add_input_rules()
	{
		// Grab the rules from the formo config
		$rules = Arr::path(Kohana::config('formo'), 'input_rules.'.$this->get_type());

		if ($rules)
		{
			// Attach rules to the field's parent
			$this->field->parent()->rules($this->field->alias(), $rules);
		}
		
		if ($bindings = Kohana::config('formo.html5_bindings.'.$this->get_type()))
		{
			$this->field->set('bindings', $bindings);
		}
	}

}