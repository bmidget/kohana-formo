<?php defined('SYSPATH') or die('No direct script access.');

class Formo_ORM_Factory_Core {

	public static $driver;
	public static $config = array();

	// Create and return the proper ORM driver
	public static function factory($form)
	{
		// Discover the driver and config array
		self::fill_vars($form);
		
		// Return the new driver instance
		return new self::$driver($form);
	}
	
	protected static function fill_vars($form)
	{
		if (self::$driver === NULL)
		{
			self::$driver = $form->get('config')->orm_driver;
		}
		
		if (empty(self::$config))
		{
			self::$config = Kohana::config($form->get('config')->orm_config);
		}
	}

}