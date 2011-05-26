<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Color extends Formo_Driver {

	protected $view = 'color';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'color')
			->attr('id', $this->name())
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}