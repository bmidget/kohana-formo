<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Bool_Core extends Formo_Driver_Core {

	protected $view = 'bool';
		
	public function checked()
	{
		if ($this->field->parent(Formo_Container::PARENT)->sent() AND $this->field->get('new_value') == Formo_Container::NOTSET)
			return FALSE;
						
		return $this->val() == 1;
	}
	
	// Make the field checked
	public function check()
	{
		// Set this value to 1
		$this->field->set('value', 1);
	}
	
	public function uncheck()
	{
		$this->field->set('value', 0);
	}
	
	protected function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->render_field->alias())
			->attr('value', 1);
		
		$parent_value = $this->render_field->parent()->val();
				
		if ($this->field->checked())
		{
			$this->render_field->attr('checked', 'checked');
		}
	}

}