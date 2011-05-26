<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Textarea_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Textarea extends Formo_Driver {

	protected $view = 'textarea';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'textarea')
			->set('text', $this->field->val())
			->attr('id', $this->name())
			->attr('name', $this->name());
	}

}