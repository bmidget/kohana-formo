<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Option_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Option extends Formo_Driver {

	protected $_view_file = 'option';

	public function html()
	{
		$this->_view
			->set_var('tag', 'option')
			->text($this->_field->alias())
			->attr('value', $this->_field->val());

		if ($this->_field->parent()->val() == $this->_field->val())
		{
			$this->_field->view()->attr('selected', 'selected');
		}
	}
	
	public function format_alias($alias)
	{
		return $alias;
	}

}
