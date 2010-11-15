<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Button_Core extends Formo_Driver {

	protected $view = 'button';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'button')
			->attr('name', $this->name())
			->text($this->field->alias());
	}

}