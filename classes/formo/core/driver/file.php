<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_File_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_File extends Formo_Driver {

	protected $view = 'file';
	public $file = TRUE;
	
	public function html_append()
	{
		$this->field->parent()->attr('enctype', 'multipart/form-data');
	}
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'file')
			->attr('name', $this->field->alias());
	}
	
	public function setval($value)
	{
		// Check for the appropriate "$_FILES" entry
		$this->set('new_value', $value['name']);
	}

}