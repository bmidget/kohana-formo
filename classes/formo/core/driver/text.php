<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Text extends Formo_Driver {

	protected $_view_file = 'text';
		
	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'text')
			->attr('name', $this->name())
			->attr('value', $this->_field->val());
	}

}