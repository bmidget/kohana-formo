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
	// Does this kind of field potentially not have a post value
	public $empty_input = FALSE;
		
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
		
		return ($new_value !== Formo::NOTSET)
			? $new_value
			: $this->field->get('value');
	}
					
	// Set and retrieve a field's value
	public function val($value = NULL)
	{
		if (func_num_args() === 0)
			return $this->getval();
					
		foreach ($this->field->get_filter('pre') as $filter)
		{
			// Resolve pseudo args
			$this->field->pseudo_args($filter->args, array(':value' => $value));
			
			// Run the filters
			$value = $filter->execute();
		}

		// Set the value
		$this->field->set('new_value', $value);
		
		if ($this->field->model() AND $orm = $this->field->orm)
		{
			// If the value needs to be set in the model, do that too
			$orm->set_field($this->field, $value);
		}
		
		return $this;
	}
	
	public function newval_set()
	{
		return $this->field->get('new_value') !== Formo::NOTSET;
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
		$subform = Formo::form($alias, $driver);
		
		foreach ($fields as $key => $field)
		{
			if (is_string($key) AND ! ctype_digit($key))
			{
				// Pull fields "as" a new alias
				$new_alias = $field;
				$field = $key;
			}
			
			// Find each field
			$field = $this->field->find($field);
			
			if ( ! empty($new_alias))
			{
				// Set the new alias
				$field->alias($new_alias);
			}
			
			// Remember the field's original parent
			$last_parent = $field->parent();
			
			// Add the field to the new subform
			$subform->append($field);
			
			// Remove the field from its original parent
			$last_parent->remove($field->alias());
		}
		
		// If the parent has a model, copy it to the new subform
		$subform->set('model', $this->field->get('model'));
		
		// Add the order if applicable
		($order AND $subform->set('order', $order));
		
		// Append the new subform		
		$this->field->append($subform);
		
		return $this->field;
	}
	
	public function orm($method)
	{
		$args = array_slice(func_get_args(), 1);
		return call_user_func_array(array($this->field->orm, $method), $args);
	}
	
	public function pre_render($type)
	{
		$this->render_type = $type;
		
		$this->render_field = Formo::render_obj($type, $this->field);
		$this->render_field->set('fields', $this->field->fields());
		
		if ($this->field->orm)
		{
			$this->field->orm->pre_render();
		}
		
		// Grab the value
		$value = $this->field->val();

		// Run display_filters
		foreach ($this->field->get_filter('display') as $filter)
		{
			// Resolve parameters
			$this->field->pseudo_args($filter->args);
			// Run the filter
			$value = $filter->execute();
		}
		
		$this->render_field->value = $value;
						
		// Run the type-specific method for further setup, ex: $this->html()
		(method_exists($this, $type) AND $this->$type());
	}

	public function view($type)
	{
		// First run the pre_render method
		$this->pre_render($type);
		
		$prefix = ($_prefix = $this->field->get('view_prefix'))
			? $_prefix
			: $_prefix = $this->field->parent(Formo::PARENT)->get('view_prefix');
		
		$prefix = rtrim($prefix, '/');
			
		$view = ($this->field->get('view')) ? $this->field->get('view') : $this->view;
		
		return View::factory("$prefix/html/$view")
			->bind($this->alias, $this->render_field);
	}
	
	public function not_empty()
	{
		$new_value = $this->field->get('new_value');
		
		if (Formo::notset($new_value) AND ! $this->field->get('value'))
			return FALSE;
			
		return (bool) $new_value;
	}
}