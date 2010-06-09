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
		
	public static function factory(Formo_Container $field)
	{
		return new Formo_Driver($field);
	}
	
	public function __construct(Formo_Container $field)
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

	// Run when loading input data
	public function load($value)
	{
		// Just set the value to what was passed
		$this->val($value);
	}
	
	// Retrieve a field's value
	public function getval()
	{
		$new_value = $this->field->get('new_value');
		
		return ($new_value !== Formo_Container::NOTSET)
			? $new_value
			: $this->field->get('value');
	}
					
	// Set and retrieve a field's value
	public function val($value = NULL)
	{
		if (func_num_args() === 0)
			return $this->getval();

		// Set the value
		$this->field->set('new_value', $value);
		
		if ($model = $this->field->model())
		{
			// If the value needs to be set in the model, do that too
			$model->{$this->field->alias()} = $value;
		}
		
		return $this;
	}
	
	// Make every option an array of options
	public function set_options($options)
	{
		// Create the new array
		$array = array();
		foreach ($options as $alias => $value)
		{
			$array[$alias] = ( ! is_array($value))
				// Make the value part of an array 
				? array('value' => $value)
				: $value;
		}
		
		return $array;
	}

	// Create a subform from fields already in the Container object
	public function create_sub($alias, $driver, array $fields, $order = NULL)
	{
		// Create the empty subform object
		$subform = Formo::factory($alias, $driver);
		
		foreach ($fields as $field)
		{
			// Find each field
			$field = $this->find($field);
			// Remember the field's original parent
			$last_parent = $field->parent();
			
			// Add the field to the new subform
			$subform->append($field);
			
			// Remove the field from its original parent
			$last_parent->remove($field->alias());
		}
		
		// If the parent has a model, copy it to the new subform
		$subform->set('model', $this->get('model'));
		
		// Add the order if applicable
		($order AND $subform->set('order', $order));
		
		// Append the new subform		
		$this->append($subform);
		
		return $this;
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
			: $this->field->parent(Formo_Container::PARENT)->get('view_prefix');
			
		return View::factory("formo/html/$this->view")
			->bind($this->alias, $this->render_field);
		
		return View::factory("$prefix$this->render_type/$this->view")
			->bind($this->alias, $this->render_field);
	}	
}