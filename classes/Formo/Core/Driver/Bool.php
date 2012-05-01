<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Bool_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Bool extends Formo_Driver {

	protected $_view_file = 'bool';
	public $empty_input = TRUE;
		
	public function checked()
	{
		// Check if field was sent. If so, the new value shoulda been posted
		if ($this->_field->sent() AND Formo::is_set($this->_field->get('new_value')) === FALSE)
			return FALSE;
						
		return $this->val() == TRUE;
	}
	
	protected function _get_val()
	{
		$new_value = $this->_field->get('new_value');

		// If the form was sent but the field wasn't set, return FALSE
		if ($this->_field->sent() AND Formo::is_set($new_value) === FALSE)
			return FALSE;
			
		// Otherwise return the value that's set
		return (Formo::is_set($new_value) === TRUE)
			? (bool) $new_value
			: (bool) $this->_field->get('value');
	}
	
	public function not_empty()
	{
		// If it's checked, it is not empty
		return $this->checked() === TRUE;
	}
	
	// Make the field checked
	public function check()
	{
		// Set this value to 1
		$this->_field->set_var('value', TRUE);
	}
	
	public function uncheck()
	{
		$this->_field->set_var('value', 0);
	}
	
	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->name())
			->attr('value', 1);
		
		$parent_value = $this->_field->parent()->val();
		
		if ($this->_field->checked())
		{
			$this->_view->attr('checked', 'checked');
		}
	}

}