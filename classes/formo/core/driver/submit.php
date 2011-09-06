<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Submit_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Submit extends Formo_Driver {

	protected $_view_file = 'submit';

	public function html()
	{
		$this->_view
			->set_var('tag', 'button')
			->set_var('text', ($text = $this->_field->val()) ? $text : $this->_view->label())
			->attr('type', 'submit')
			->attr('name', $this->name());
	}

	public function sent()
	{
		return $this->_field->not_empty() !== FALSE;
	}

}
