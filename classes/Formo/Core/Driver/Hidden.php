<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Hidden_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Hidden extends Formo_Driver {

	protected $_view_file = 'hidden';

	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'hidden')
			->attr('name', $this->name())
			->attr('value', $this->_field->val());
	}

}