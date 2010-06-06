<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Textarea_Core extends Formo_Driver {

	protected $view = 'textarea';
	
	public function html()
	{
		$this->render_field
			->set('tag', 'textarea')
			->set('text', htmlentities($this->render_field->val()))
			->attr('name', $this->render_field->alias());
	}

}