<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radios_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Radios extends Formo_Driver {

	protected $view = 'radios';
	
	public function html()
	{
		foreach ($this->field->get('options') as $label => $options)
		{
			$this->field->append(Formo::field($label, 'radio', $options));
		}
	}

}