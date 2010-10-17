<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Password_Core class.
 *
 * @package  Formo
 */
class Formo_Driver_Password_Core extends Formo_Driver {

	protected $view = 'text';

	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'password')
			->attr('name', $this->name());
	}

}
