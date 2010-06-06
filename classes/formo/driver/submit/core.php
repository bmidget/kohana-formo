<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Submit_Core extends Formo_Driver {

	protected $view = 'submit';

	public function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'submit')
			->attr('name', $this->render_field->alias())
			->attr('value', htmlentities($this->render_field->val()));
	}

}