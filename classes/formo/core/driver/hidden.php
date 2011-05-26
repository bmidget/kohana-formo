<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Hidden_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Hidden extends Formo_Driver {

	protected $view = 'hidden';

	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'hidden')
			->attr('id', $this->name())
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}