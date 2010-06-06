<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Radio_Core extends Formo_Driver {

	protected $view = 'radio';

	public function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'radio')
			->attr('name', $this->render_field->parent()->alias())
			->attr('value', htmlentities($this->render_field->val()));
			
		if ($this->render_field->parent()->val() == $this->render_field->val())
		{
			$this->render_field->attr('checked', 'checked');
		}
	}

}