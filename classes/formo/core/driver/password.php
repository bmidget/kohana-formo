<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Password_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Password extends Formo_Driver {

	protected $view = 'text';

	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('id', $this->name())
			->attr('type', 'password')
			->attr('name', $this->name());
	}

}
