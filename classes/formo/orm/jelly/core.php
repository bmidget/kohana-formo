<?php defined('SYSPATH') or die('No direct script access.');

class Formo_ORM_Jelly_Core extends Formo_ORM {

	// A parent form or subform
	protected $form;
	// The model associated with the form
	public $model;
	
	// Fields to load
	protected $fields = array();
	// Fields to skip altogether
	protected $skip_fields = array();
	
	// This is instantiated from Formo::load_orm
	public function __construct($form)
	{
		$this->form = $form;
	}
	
	// Load a model's fields
	public function load(Jelly_Model $model, array $fields = NULL)
	{
		$this->model = $model;
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
			$validation_keys = $this->config()->validation_keys;
			
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
			if ($field instanceof Jelly_Field_Relationship === FALSE)
			{
				// Add the value
				$options['value'] = ($model->get($column))
					? $model->get($column)
					: $options['default'];
			}
			// Only perform this on BelongsTo and HasOne relationships
			elseif ($field instanceof Field_ManyToMany === FALSE AND $field instanceof Field_HasMany === FALSE)
			{
				// grab the actual foreign model
				$foreign_model = $model->get($column)->execute();
				// Set the value
				$options['value'] = $foreign_model->{$foreign_model->meta()->primary_key()};
			}
			else
			{
				// Grab the foreign records
				$foreign_models = $model->get($column)->execute();
				// Create the array
				$values = array();
				foreach ($foreign_models as $record)
				{
					$values[$record->get($record->meta()->name_key())] = $record->get($record->meta()->primary_key());
				}
				
				$options['value'] = $values;
			}
			
			// Convert value to at a string if it was an object
			(is_object($options['value']) AND $options['value'] = (string) $options['value']);
			
			// Add the field to its parent
			$this->form->add($column, $options);
			
			$field = $this->form->{$column};
			
			$this->add_auto_rules($field);			
		}
		
		return $this->form;
	}
	
	// This adds turns fills relational fields with relations to choose from
	public function pre_render()
	{
		if ((bool) $this->model === FALSE)
			// Do nothing if the model is not set
			return;
			
		foreach ($this->form->fields() as $field)
		{
			if ( ! $data = $this->model->meta()->fields($field->alias()))
				// If field doesn't exist continue
				continue;
				
			if ( ! $data instanceof Jelly_Field_Relationship)
				// If field is not a relationship, continue
				continue;
						
			// Fetch the list of all available records
			$records = Jelly::select($field->foreign['model'])
				->order_by(':name_key')
				->execute();
			
			// Create the array
			$options = array();
			foreach ($records as $record)
			{
				// Set the option
				$options[$record->{$record->meta()->name_key()}] = $record->{$record->meta()->primary_key()};
			}
			
			// Set all available options
			$field->set('options', $options);
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
	protected function add_auto_rules(Formo_Container $field)
	{
		foreach ($this->config()->auto_rules as $parameter => $values)
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
		return ( ! empty($this->config()->drivers[$class]))
			// Return the default driver
			? $this->config()->drivers[$class]
			// Otherwise return the genral form default driver
			: $this->form->get('config')->default_driver;
	}
		
	// Push a form's values into a model
	public function pull( & $model, $model_name = NULL)
	{
		if ($model instanceof Jelly_Model === FALSE)
		{
			// If model is not an object, create the object
			$model = Jelly::factory($model_name);
		}
		
		foreach ($this->form->as_array('value') as $alias => $value)
		{
			if ($model->meta()->fields($alias) !== NULL)
			{
				// Add form values to the model
				$model->$alias = $value;
			}			
		}
		
		return $model;
	}
			
}