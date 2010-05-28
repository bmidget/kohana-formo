<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Driver_Core {

	// The field or form object
	protected $field;
	protected $view;
	
	public static function factory(Container $field)
	{
		return new Formo_Driver($field);
	}
	
	public function __construct(Container $field)
	{
		$this->field = $field;
	}
	
	// Call the function on the $this->field
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->field, $method), $args);
	}
	
	public function __set($variable, $value)
	{
		$this->field->$variable = $value;
	}
	
	public function __get($variable)
	{
		return $this->field->$variable;
	}
	
	public function view()
	{
		$prefix = $this->parent(CONTAINER::PARENT)->get('view_prefix');
		$view = $this->get('view') ? $this->get('view') : $this->view;

		return $prefix.$this->get('render_type').'/'.$view;
	}
		
	public function add_post($value)
	{
		$this->set('value', $value);
		
		if ($model = $this->model())
		{
			$model->{$this->alias()} = $value;
		}
	}
	
	public function pre_render()
	{
		foreach ($this->field->get_validator('post_filters') as $filter)
		{
			// Execute every post filter
			$filter->execute();
		}
	}
	
	public function pre_render_json($field){}
	public function pre_render_html($field){}
	public function pre_render_xml($field){}
	
}