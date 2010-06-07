<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Bool_Core extends Formo_Driver_Core {

	protected $view = 'bool';
		
	public function getval()
	{
		// The value is always the same
		return $this->field->get('value');
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