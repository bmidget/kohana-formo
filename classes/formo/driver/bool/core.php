<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Bool_Core extends Formo_Driver_Core {

	protected $view = 'bool';
		
	public function getval()
	{
		// The value is always the same
		return $this->field->get('value');
	}

	public function checked()
	{
		if ( ! $this->field->parent(Formo_Container::PARENT)->sent() AND $this->field->get('new_value') === Formo_Container::NOTSET)
			return (bool) $this->field->get('checked');
		
		return $this->field->get('new_value') == 'on';
	}
	
	// Make the field checked
	public function check()
	{
		$this->field->set('checked', TRUE);
	}
	
	public function uncheck()
	{
		$this->field->set('checked', FALSE);
	}
	
	protected function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->render_field->alias())
			->attr('value', htmlentities($this->render_field->val()));
		
		$parent_value = $this->render_field->parent()->val();
				
		if ($this->field->checked())
		{
			$this->render_field->attr('checked', 'checked');
		}
	}

}