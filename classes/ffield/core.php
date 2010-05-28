<?php defined('SYSPATH') or die('No direct script access.');

class Ffield_Core extends Container {
	
	public $options = array();
	public $attr = array();
	public $css = array();
	
	protected $_settings = array
	(
		'driver'		=> 'text',
		'render_type'	=> 'html',
		'value'			=> NULL,
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
	}
	
	public function __toString()
	{
		return $this->render(TRUE);
	}
	
	public function render($render_type)
	{
		$render_type = $this->parent()->get('render_type');
		$this->set('render_type', $render_type);

		$this->driver->pre_render();

		$class = 'Formo_Render_'.$render_type;
		$method = 'pre_render_'.$render_type;
		$render_obj = new $class($this);
		
		$this->driver->$method($render_obj);
		
		$view = View::factory($this->driver->view())
			->bind('field', $render_obj);
		
		return $view;
	}

}