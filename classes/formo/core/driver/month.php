<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Month extends Formo_Driver {

	protected $view = 'month';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'month')
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}