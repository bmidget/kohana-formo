<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Driver_Core {

	protected $view;
	// The field or form object
	protected $field;
	
	// The render object
	protected $render_field;
	// Render type
	protected $render_type;
	// Render object name passed to view
	protected $alias = 'field';
	
	public static function factory(Container $field)
	{
		return new Formo_Driver($field);
	}
	
	public function __construct(Container $field)
	{
		// Load the field instance
		$this->field = $field;
	}
	
	// Called at field's construct. Gives driver chance to do stuff
	public function post_construct(){}
	// Called just before running validate()
	public function pre_validate(){}
	// Called just after running validate()
	public function post_validate(){}
	
	// Retrieve a field's value
	public function getval()
	{
		$new_value = $this->field->get('new_value');
		
		return ($new_value !== Container::NOTSET)
			? $new_value
			: $this->field->get('value');
	}
					
	// Set a field's value
	public function val($value)
	{
		// Set the value
		$this->field->set('new_value', $value);
		
		if ($model = $this->field->model())
		{
			// If the value needs to be set in the model, do that too
			$model->{$this->field->alias()} = $value;
		}
	}
	
	public function pre_render($type)
	{
		$this->render_type = $type;
		foreach ($this->field->get_validator('post_filters') as $filter)
		{
			// Execute every post filter
			$filter->execute();
		}
		
		$render_field_class = Kohana::config('formo')->render_classes[$type];
		$this->render_field = new $render_field_class($this->field);
		$this->render_field->set('fields', $this->field->get('fields'));
		
		if (method_exists($this, $type))
			return $this->$type();
	}

	public function view()
	{
		$prefix = ($_prefix = $this->field->get('view_prefix'))
			? $_prefix
			: $this->field->parent(CONTAINER::PARENT)->get('view_prefix');
		
		return View::factory("$prefix$this->render_type/$this->view")
			->bind($this->alias, $this->render_field);
	}	
}