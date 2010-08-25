<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Field_Core class.
 * 
 * @package  Formo
 */
class Formo_Field_Core extends Formo_Validator {
	
	/**
	 * Group items as in select options, individual radios, individual checkboxes, etc.
	 * 
	 * (default value: array())
	 * 
	 * @var array
	 * @access public
	 */
	public $options = array();
	
	/**
	 * Field-specific settings
	 * 
	 * @var mixed
	 * @access protected
	 */
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
		
	/**
	 * Create a new field
	 * 
	 * @access public
	 * @param mixed $alias
	 * @param mixed $driver. (default: NULL)
	 * @param mixed array $options. (default: NULL)
	 * @return void
	 */
	public function __construct($alias, $driver = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = $options;
		$options = Formo::args(__CLASS__, __FUNCTION__, $options);
		
		// Add all the options to the object
		$this->load_options($options);
		
		// Run the driver's post_construct() method
		$this->driver->post_construct();
	}
	
	/**
	 * Render the field according to default render_type
	 * 
	 * @access public
	 * @return view object
	 */
	public function __toString()
	{
		// Render as the default render type
		return (string) $this->render(Kohana::config('formo')->render_type);
	}
	
	/**
	 * Overloaded sent method
	 * 
	 * @access public
	 * @param mixed array $input. (default: NULL)
	 * @return bool
	 */
	public function sent(array $input = NULL)
	{
		// Always return whether the parent was sent
		return $this->parent()->sent();
	}
	
	/**
	 * Overloaded message_file
	 * 
	 * @access public
	 * @return string
	 */
	public function message_file()
	{
		// Always return the parent's message file if this one's isn't defined
		return $this->get('message_file')
			? $this->get('message_file')
			: $this->parent()->message_file();
	}
	
	/**
	 * Turn field into a view file for rendering
	 * 
	 * @access public
	 * @param mixed $render_type
	 * @return view object
	 */
	public function render($render_type)
	{
		if ($this->get('render') === FALSE)
			return;
			
		$view = $this->driver->view($render_type);
		
		return $view;
	}

}