<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Textarea_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Textarea extends Formo_Driver {

	protected $_view_file = 'textarea';
	
	public function html()
	{
		$this->_view
			->set_var('tag', 'textarea')
			->set_var('text', $this->_field->val())
			->attr('name', $this->name());
	}

}