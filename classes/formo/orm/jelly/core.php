<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_ORM_Jelly_Core class.
 *
 * @package  Formo
 */
class Formo_ORM_Jelly_Core extends Formo_ORM {

	/**
	 * A parent form or subform
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $form;

	/**
	 * The model associated with the form
	 *
	 * @var mixed
	 * @access public
	 */
	public $model;

	/**
	 * Fields to load
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $fields = array();

	/**
	 * Fields to skip altogether
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $skip_fields = array();

	/**
	 * Instantiated from Formo::load_orm
	 *
	 * @access public
	 * @param mixed $form
	 * @return void
	 */
	public function __construct($form)
	{
		$this->form = $form;
	}

	/**
	 * Load a model's fields into the form
	 *
	 * @access public
	 * @param mixed Jelly_Model $model
	 * @param mixed array $fields. (default: NULL)
	 * @return form object
	 */
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
				$options['value'] = $foreign_model->id();
			}
			else
			{
				// Grab all the foreign options
				$all_options = Jelly::select($field->foreign['model'])->execute();

				// Create the array
				$options['options'] = array();
				$options['value'] = array();
				foreach ($all_options as $option)
				{
					// Build the option
					$options['options'][] = array
					(
						'value' => $option->id(),
						'alias' => $option->name(),
					);

					if ($model->has($column, $option))
					{
						$options['value'][] = $option->id();
					}
				}
			}

			// Add the field to its parent
			$this->form->add($column, $options);

			$field = $this->form->{$column};
		}

		return $this->form;
	}

	/**
	 * Set all field's values to correspond with formo values
	 *
	 * @access public
	 * @param mixed Formo $field
	 * @param mixed $value
	 * @return object
	 */
	public function set_field(Formo_Container $field, $value)
	{
		$column = $field->get('alias');

		if ( ! $column)
			return $this;

		$data = $this->model->meta()->fields($column);

		if ($data instanceof Jelly_Field_ManyToMany OR $data instanceof Jelly_Field_HasMany)
		{
			// Run through each possibility and add/remove as necessary
			foreach (Jelly::select($field->foreign['model'])->execute() as $record)
			{
				// Determine whether to add or remove the record
				$method = (in_array($record->id(), (array) $value))
					? 'add'
					: 'remove';

				// Run the add/remove method
				$this->model->$method($column, $record);
			}

			return $this;
		}

		// Simple field, just set the data
		$this->model->{$column} = $value;

		return $this;
	}

	/**
	 * Adds turns fills relational fields with relations to choose from
	 *
	 * @access public
	 * @return void
	 */
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
				$options[$record->name()] = $record->id();
			}

			// Set all available options
			$field->set('options', $options);
		}
	}

	/**
	 * Determine which fields to add, which to skip
	 *
	 * @access protected
	 * @param mixed array $fields. (default: NULL)
	 * @return void
	 */
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

	/**
	 * Add any auto_rules
	 *
	 * @access protected
	 * @param mixed Formo_Container $field
	 * @return void
	 */
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

	/**
	 * Determine which driver should be used
	 *
	 * @access protected
	 * @param mixed array $options
	 * @param mixed $class
	 * @return string
	 */
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

	/**
	 * Push a form's values into a model
	 *
	 * @access public
	 * @param mixed & $model
	 * @param mixed $model_name. (default: NULL)
	 * @return void
	 */
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
