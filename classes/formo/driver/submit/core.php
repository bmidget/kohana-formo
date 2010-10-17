<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Submit_Core class.
 * 
 * @package  Formo
 */
class Formo_Driver_Submit_Core extends Formo_Driver {

	protected $view = 'submit';

	public function html()
	{
		$value = ($val = $this->field->get('value'))
			? $val
			: $this->decorator->label();
			
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'submit')
			->attr('name', $this->name())
			->attr('value', $value);
	}

}