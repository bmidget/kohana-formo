<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_ORM_Core class.
 * 
 * @package   Formo
 * @category  ORM
 */
abstract class Formo_Core_ORM implements Formo_ORM_interface {

	/**
	 * Fetch the proper orm config file
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _config()
	{
		return Kohana::$config->load(Kohana::$config->load('formo')->orm_config);
	}

}