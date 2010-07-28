<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radios_Core class.
 * 
 * @extends Formo_Driver
 * @package Formo
 */
class Formo_Driver_Radios_Core extends Formo_Driver {

	protected $view = 'radios';
	
	public function html()
	{
		foreach ($this->render_field->options as $label => $options)
		{				
			$this->render_field->append(Formo::field($label, 'radio', $options));
		}
	}

}