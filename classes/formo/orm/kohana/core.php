<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_ORM_Kohana_Core extends Formo_ORM {

	protected $form;
	public $model;
	
	// Relationship items
	protected static $relationship_types = array('has_many', 'belongs_to', 'has_one');
	protected $has_many = array();
	protected $belongs_to = array();
	protected $has_one = array();
	
	// Validation items
	protected $rules = array();
	protected $labels = array();
	// Formo meta data from the model
	protected $formo = array();
	
	protected $fields = array();
	protected $skip_fields = array();
	
	public function __construct($form)
	{
		$this->form = $form;
	}
	
	public function load(ORM $model, array $fields = NULL)
	{
		$this->model = $model;
		$this->make_fields($fields);
		$this->load_meta();
		
		foreach ($model->as_array() as $alias => $value)
		{
			// Create the array
			$options = array();

			if (isset($this->rules[$alias]))
			{
				$rules = array();
				foreach ($this->rules[$alias] as $callback => $rule)
				{
					// Set up the rules array
					$rules[$callback] = $rule;
				}
				
				// Properly merge rules in to the options array
				$options += array('rules' => $rules);
			}
			
			if ( ! empty($this->formo[$alias]))
			{
				// Merge field-specific formo meta data to the options array
				$options += $this->formo[$alias];
			}

			if (empty($options['driver']))
			{
				// Default to the default driver
				$options['driver'] = $this->config()->drivers['default'];
			}

			$this->form
				->add($alias, $options);
		}
		
		return $this->form;
	}

	public function set_field(Formo_Container $field, $value)
	{
	
	}

	/**
	 * Determine the field type for a table field
	 * 
	 * @access protected
	 * @param mixed $alias
	 * @return string
	 */
	protected function field_type($alias)
	{
		foreach (self::$relationship_types as $type)
		{
			if (isset($this->$type[$alias]))
				return $type;
		}
		
		return NULL;
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
	 * Load validate and relationship data for easy access
	 * 
	 * @access protected
	 * @return void
	 */
	protected function load_meta()
	{
		// First load the relationships
		foreach (self::$relationship_types as $type);
		$this->$type = $this->model->$type();
		
		// Then load the validate definitions
		$this->rules = $this->model->rules();
		$this->labels = $this->model->labels();
		
		if (isset($this->model->_formo))
		{
			// The formo meta data
			$this->formo = $this->model->_formo;
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
	
	public function pre_render()
	{
	
	}
}