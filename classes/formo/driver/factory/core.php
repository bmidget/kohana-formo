<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Factory_Core {

	protected static $prefix = 'Formo_Driver_';

	// Returns the correct driver object
	public static function factory($form, $driver = NULL)
	{
		// Setup options array
		$options = func_get_args();
		$options = Formo_Container::args(__CLASS__, __FUNCTION__, $options);
		
		$driver_name = self::$prefix.$options['driver'];
						
		return new $driver_name($form);
	}
	
	// Compare a driver instance with a field's current driver
	public static function is_driver($instance, $driver)
	{
		return strtolower(get_class($instance)) == strtolower(self::$prefix.$driver);
	}

}