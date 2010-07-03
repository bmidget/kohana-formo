<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_ORM_Core implements Formo_ORM_interface {

	/**
	 * Fetch the proper orm config file
	 * 
	 * @access protected
	 * @return void
	 */
	protected function config()
	{
		return Kohana::config(Kohana::config('formo')->orm_config);
	}

}