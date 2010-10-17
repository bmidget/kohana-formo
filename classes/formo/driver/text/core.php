<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package  Formo
 */
class Formo_Driver_Text_Core extends Formo_Driver {

	protected $view = 'text';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'text')
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}