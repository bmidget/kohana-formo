<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Password_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Password extends Formo_Driver {

	protected $_view_file = 'input';

	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'password')
			->attr('name', $this->name());
	}

}
