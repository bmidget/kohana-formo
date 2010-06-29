<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Radio_Core extends Formo_Driver {

	protected $view = 'radio';

	public function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'radio')
			->attr('name', $this->field->parent()->alias())
			->attr('value', HTML::entities($this->render_field->value));
						
		if ($this->field->parent()->val() == $this->render_field->value)
		{
			$this->render_field->attr('checked', 'checked');
		}
	}

}