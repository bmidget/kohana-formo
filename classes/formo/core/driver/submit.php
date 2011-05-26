<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Submit_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Submit extends Formo_Driver {

	protected $view = 'submit';

	public function html()
	{
		$value = ($val = $this->field->get('value'))
			? $val
			: $this->decorator->label();
			
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'submit')
			->attr('id', $this->name())
			->attr('name', $this->name())
			->attr('value', $value);
	}
	
	public function sent()
	{
		return $this->field->not_empty() !== FALSE;
	}

}