<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_File_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_File extends Formo_Driver {

	protected $_view_file = 'file';
	public $file = TRUE;
	
	public function html_append()
	{	
		$this->_field->parent()->view()->attr('enctype', 'multipart/form-data');
	}
	
	public function html()
	{
		$this->_view
			->set_var('tag', 'input')
			->attr('type', 'file')
			->attr('name', $this->_field->alias());
	}
	
	public function setval($value)
	{
		// Check for the appropriate "$_FILES" entry
		$this->set_var('new_value', $value['name']);
	}
	
	protected function _get_val()
	{
		$new_value = $this->_field->get('new_value');

		if (Formo::is_set($new_value) === TRUE)
			return $new_value;
		
		return ($val = $this->_field->get('value'))
			? $val
			: array('name' => '', 'type' => '', 'tmp_name' => '', 'error' => '', 'size' => '');
	}

}