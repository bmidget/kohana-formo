<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Button_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Button extends Formo_Driver {

	protected $view = 'button';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'button')
			->attr('name', $this->name())
			->attr('value', $this->val())
			->text($this->field->alias());
	}

	public function sent()
	{
		return $this->field->not_empty() !== FALSE;
	}

}