<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Option_Core extends Formo_Driver {

	protected $view = 'option';

	public function html()
	{
		$this->render_field
			->set('tag', 'option')
			->text($this->render_field->alias())
			->attr('value', HTML::entities($this->render_field->value));
			
		if ($this->render_field->parent()->val() == $this->render_field->val())
		{
			$this->render_field->attr('selected', 'selected');
		}
	}

}