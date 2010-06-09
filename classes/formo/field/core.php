<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Field_Core extends Formo_Validator {
	
	public $options = array();
	protected $_settings = array
	(
		'driver'		=> 'text',
		'render_type'	=> 'html',
		'render'		=> TRUE,
		'editable'		=> TRUE,
		'value'			=> NULL,
		'new_value'		=> Formo_Container::NOTSET,
	);
	
	public static function factory($alias, $driver = NULL, array $options = NULL)
	{
		return new Formo_Field($alias, $driver, $options);
	}
	
	public function __construct($alias, $driver = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = $options;
		$options = Formo_Container::args(__CLASS__, __FUNCTION__, $options);
				
		$this->load_options($options);
				
		$this->driver->post_construct();
	}
			
	public function __toString()
	{
		return $this->render(TRUE);
	}
	
	public function render($render_type)
	{
		if ($this->get('render') === FALSE)
			return;
			
		$this->driver->pre_render($render_type);
		$view = $this->driver->view();
		
		return $view;
	}

}