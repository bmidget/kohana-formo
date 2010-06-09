<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkbox_Core extends Formo_Driver {

	protected $view = 'checkbox';
	
	public function checked()
	{
		$parent_newval = $this->field->parent()->get('new_value');
		$parent_value = $this->field->parent()->get('value');
		
		if ($parent_newval === Formo_Container::NOTSET AND ! $this->field->parent(Formo_Container::PARENT)->sent())
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
			->attr('value', htmlentities($this->render_field->val()));
		
		$parent_value = $this->render_field->parent()->val();
				
		if ($this->field->checked())
		{
			$this->render_field->attr('checked', 'checked');
		}
	}

}