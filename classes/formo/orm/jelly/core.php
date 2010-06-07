<?php defined('SYSPATH') or die('No direct script access.');

class Formo_ORM_Jelly_Core {

	// A parent form or subform
	protected $form;
	
	// Fields to load
	protected $fields = array();
	// Fields to skip altogether
	protected $skip_fields = array();
	
	// This is instantiated from the Formo_ORM_Factory class
	public function __construct($form)
	{
		$this->form = $form;
	}
	
	// Load a model's fields
	public function load(Jelly_Model $model, array $fields = NULL)
	{
		$this->make_fields($fields);
		
		foreach ($model->meta()->fields() as $column => $field)
		{
			if (in_array($column, $this->skip_fields))
				continue;
				
			if ($this->fields AND ( ! in_array($column, $this->fields)))
				continue;
			
			// Create the array
			$options = (array) $field;
			
			// Fetch the validation key names from the config file
			$validation_keys = Formo_ORM_Factory::$config->validation_keys;
			
			// Look for validation rules as defined by the config file
			foreach ($validation_keys as $key => $value)
			{
				// If they are using the assumed names, do nothing
				if ($key === $value)
					continue;
									
				// Only grab the proper validation settings from jelly field definition
				$options[$key] = ( ! empty($options[$value]))
					? $options[$value]
					: array();
				
				// No need to carry duplicates for a rule
				unset($options[$value]);
			}
			
			// Determine the driver
			$options['driver'] = $this->determine_driver($options, get_class($field));
			
			// Add the value
			$options['value'] = ($model->get($column))
				? $model->get($column)
				: $options['default'];
			
			// Convert value to at a string if it was an object	
			(is_object($options['value']) AND $options['value'] = (string) $options['value']);
										
			// Add the field to its parent
			$this->form->add($column, $options);
			
			$field = $this->form->{$column};
			
			$this->add_auto_rules($field);			
		}
	}
	
	// Determine which fields to add, which to skip
	protected function make_fields(array $fields = NULL)
	{
		// If no fields were specified, no need to continue
		if ($fields === NULL)
			return;
		
		// If * is set, the rest of the fields are skipped
		// Like "All but the other fields"
		if (in_array('*', $fields))
			return $this->skip_fields = $fields;
		
		// Set the fields to what we're fetching from the model
		return $this->fields = $fields;
	}
	
	// Add any auto_rules
	protected function add_auto_rules(Container $field)
	{
		foreach (Formo_ORM_Factory::$config->auto_rules as $parameter => $values)
		{
			list($check_value, $callback) = $values;
			
			if ($check_value instanceof Closure)
			{
				$check_value = $check_value($field);
			}
			
			// Check if the parameter indeed matches what it's supposed to
			if ($field->get($parameter) === $check_value)
			{
				// Create a new rules
				$field->rule(NULL, key($callback), current($callback));
			}
		}	
	}
	
	protected function determine_driver(array $options, $class)
	{
		// If the driver has been explicitly defined, use that
		if ( ! empty($options['driver']))
			return $options['driver'];
		
		// Check to find a default driver for a Jelly Field
		return ( ! empty(Formo_ORM_Factory::$config->drivers[$class]))
			// Return the default driver
			? Formo_ORM_Factory::$config->drivers[$class]
			// Otherwise return the genral form default driver
			: $this->form->get('config')->default_driver;
	}
	
	// Pull a form's values into a model
	public function pull( & $model, $model_name = NULL)
	{
		if ($model instanceof Jelly_Model === FALSE)
		{
			// If model is not an object, create the object
			$model = Jelly::factory($model_name);
		}
		
		foreach ($this->form->as_array('value') as $alias => $value)
		{
			// Add form values to the model
			$model->$alias = $value;
		}
		
		return $model;
	}
			
}