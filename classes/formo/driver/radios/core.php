<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radios_Core class.
 * 
 * @package  Formo
 */
class Formo_Driver_Radios_Core extends Formo_Driver {

	protected $view = 'radios';
	
	public function html()
	{
		foreach ($this->field->get('options') as $label => $options)
		{				
			$this->field->append(Formo::field($label, 'radio', $options));
		}
	}

}