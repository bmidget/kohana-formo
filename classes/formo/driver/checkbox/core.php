<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkbox_Core extends Formo_Driver {

	protected $view = 'checkbox';
	
	public function checked()
	{
		if ( ! $this->field->parent(Formo_Container::PARENT)->sent() AND $this->field->parent()->get('new_value') === Formo_Container::NOTSET)
			return (bool) $this->field->get('checked');
			
		$parent_value = $this->field->parent()->val();
		
		return (is_array($parent_value) AND in_array($this->field->val(), $parent_value));
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