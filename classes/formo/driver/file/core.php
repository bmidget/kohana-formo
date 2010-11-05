<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_File_Core extends Formo_Driver {

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