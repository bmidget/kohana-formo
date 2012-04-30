<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Button_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Button extends Formo_Driver {

	protected $_view_file = 'button';
	
	public function html()
	{
		$this->_view
			->set_var('tag', 'button')
			->attr('name', $this->name())
			->attr('value', $this->val());
		
		if ( ! $this->_view->text())
		{
			$this->_view->text($this->_field->alias());
		}
	}

	public function sent()
	{
		return $this->_field->not_empty() !== FALSE;
	}

}