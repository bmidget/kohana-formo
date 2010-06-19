<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Field_Core extends Formo_Validator {
	
	public $options = array();
	protected $_settings = array
	(
		// The field's driver
		'driver'		=> 'text',
		// The wah this field is rendered
		'render_type'	=> 'html',
		// Whether the field should be rendered
		'render'		=> TRUE,
		// Whether the field is editable
		'editable'		=> TRUE,
		// Original value added to field
		'value'			=> NULL,
		// New values added to field
		'new_value'		=> Formo::NOTSET,
		// A custom message file
		'message_file'	=> NULL,
	);
		
	public function __construct($alias, $driver = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = $options;
		$options = Formo_Container::args(__CLASS__, __FUNCTION__, $options);
		
		// Add all the options to the object
		$this->load_options($options);
		
		// Run the driver's post_construct() method
		$this->driver->post_construct();
	}
			
	public function __toString()
	{
		// Render as the default render type
		return $this->render(Kohana::config('formo')->render_type);
	}
	
	// Overloaded sent method
	public function sent(array $input = NULL)
	{
		// Always return whether the parent was sent
		return $this->parent()->sent();
	}
	
	// Overloaded message_file
	public function message_file()
	{
		// Always return the parent's message file if this one's isn't defined
		return $this->get('message_file')
			? $this->get('message_file')
			: $this->parent()->message_file();
	}
	
	public function render($render_type)
	{
		if ($this->get('render') === FALSE)
			return;
			
		$view = $this->driver->view($render_type);
		
		return $view;
	}

}