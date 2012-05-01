<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radio_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Radio extends Formo_Driver {

	protected $_view_file = 'radio';
	
	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'radio')
			->attr('name', $this->_field->parent()->name())
			->attr('value', $this->_field->val());

		if ($this->_field->parent()->val() == $this->_field->val())
		{
			$this->_field->view()->attr('checked', 'checked');
		}
	}

}
