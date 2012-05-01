<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radios_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Radios extends Formo_Driver {

	protected $_view_file = 'radios';
	
	public function html()
	{
	}
	
	public function option_name()
	{
		return $this->_field->name();
	}

}