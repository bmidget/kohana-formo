<?php defined('SYSPATH') or die('No direct script access.');

class Ffield_Core extends Validator {
	
	public $options = array();
	public $attr = array();
	public $css = array();
	
	protected $_settings = array
	(
		'driver'		=> 'text',
		'render_type'	=> 'html',
		'render'		=> TRUE,
		'editable'		=> TRUE,
	);
	
	public static function factory($alias, $driver = NULL, array $options = NULL)
	{
		return new Ffield($alias, $driver, $options);
	}
	
	public function __construct($alias, $driver = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = $options;
		$options = Container::args(__CLASS__, __FUNCTION__, $options);
				
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