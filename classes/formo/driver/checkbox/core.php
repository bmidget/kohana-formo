<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Checkbox_Core class.
 * 
 * @extends Formo_Driver
 * @package Formo
 */
class Formo_Driver_Checkbox_Core extends Formo_Driver {

	protected $view = 'checkbox';
	public $empty_input = TRUE;
	
	public function checked()
	{
		$parent_newval = $this->field->parent()->get('new_value');
		$parent_value = $this->field->parent()->get('value');
		
		if ($parent_newval === Formo::NOTSET AND ! $this->field->parent(Formo::PARENT)->sent())
			return in_array($this->val(), (array) $parent_value);
		
		return (in_array($this->field->val(), (array) $parent_newval));
	}
			
	// Setup the html field
	protected function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->field->parent()->alias().'[]')
			->attr('value', HTML::entities($this->render_field->value));
						
		if ($this->field->checked())
		{
			$this->render_field->attr('checked', 'checked');
		}
	}

}