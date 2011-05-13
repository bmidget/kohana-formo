<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Date extends Formo_Driver {

	protected $view = 'date';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'date')
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}