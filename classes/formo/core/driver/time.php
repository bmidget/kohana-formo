<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Time extends Formo_Driver {

	protected $view = 'time';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'time')
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}