<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_ORM_Kohana_Core extends Formo_ORM {

	protected $form;
	public $model;
	// Config definition from forno_kohana.php
	protected $config;

	// Relationship items
	protected static $relationship_types = array('has_many', 'belongs_to', 'has_one');
	// Current relationships
	protected $has_one = array
	(
		'definition' => array(),
		'foreign_keys' => array(),
	);
	protected $belongs_to = array
	(
		'definition' => array(),
		'foreign_keys' => array(),
	);
	protected $has_many = array
	(
		'definition' => array(),
		'foreign_keys' => array(),
	);

	// Validation items
	protected $rules = array();
	// Labes from model
	protected $labels = array();
	// Formo meta data from the model
	protected $formo = array();

	// Fields to use
	protected $fields = array();
	// Fields to skip
	protected $skip_fields = array();

	public function __construct($form)
	{
		$this->form = $form;
	}

	/**
	 * Load fields into the form
	 * 
	 * @access public
	 * @param mixed ORM $model
	 * @param mixed array $fields. (default: NULL)
	 * @return void
	 */
	public function load(ORM $model, array $fields = NULL)
	{
		$this->model = $model;
		$this->config = Kohana::config('formo_kohana');
		$this->make_fields($fields);
		$this->load_meta();

		foreach ($model->as_array() as $alias => $value)
		{
			// The bool that tracks whether the field is relational
			$relational_field = FALSE;
			// Create the array
			$options = array();
			
			// Add meta data for the field
			$this->add_meta($alias, $options);
			// Add rules from rules definition in model
			$this->add_rules($alias, $options);
			// If the field is a relational field, process it separately
			$this->process_belongs_to($alias, $options);

			if (empty($options['driver']))
			{
				// Default to the default driver
				$options['driver'] = $this->config()->drivers['default'];
			}

			$this->form
				->add($alias, $options);
		}
		
		$this->add_has_many();

		return $this->form;
	}
	
	/**
	 * Add rules to field
	 * 
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return void
	 */
	protected function add_rules($alias, array & $options)
	{
		if (empty($this->rules[$alias]))
			// Only process fields associated rules
			return;

		$rules = array();
		foreach ($this->rules[$alias] as $callback => $rule)
		{
			// Set up the rules array
			$rules[$callback] = $rule;
		}

		// Properly merge rules in to the options array
		$options += array('rules' => $rules);
	}
	
	/**
	 * Add meta data defined in model's $_formo array to applicable fields
	 * 
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return void
	 */
	protected function add_meta($alias, array & $options)
	{
		if ( ! empty($this->formo[$alias]))
		{
			$options += $this->formo[$alias];
		}
	}
	
	/**
	 * Add the correct alias if it isn't already defined
	 * 
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return void
	 */
	protected function add_alias($alias, array & $options)
	{
		if (empty($options['alias']))
		{
			$options['alias'] = $alias;
		}
	}

	public function set_field(Formo_Container $field, $value)
	{

	}

	/**
	 * Add relational data to belongs_to fields
	 * 
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return void
	 */
	protected function process_belongs_to($alias, array & $options)
	{
		if ( ! isset($this->belongs_to['foreign_keys'][$alias]))
			// No need to process non-belongs-to fields
			return;

		// The alias in the model for the field
		$field_alias = $this->belongs_to['foreign_keys'][$alias];

		// Add the alias if it wasn't already explicitly defined
		$this->add_alias($field_alias, $options);

		if (empty($options['driver']))
		{
			// If the driver hasn't already been specified, specify it
			$options['driver'] = (isset($this->config->drivers['belongs_to']))
				? $this->config->drivers['belongs_to']
				: 'select';
		}
		
		// Load options
		$query = ORM::factory($this->belongs_to['definition'][$field_alias]['model']);
		$this->add_options($query, $options);

		return;
	}
	
	/**
	 * Add has_many relationships to form
	 * 
	 * @access protected
	 * @return void
	 */
	protected function add_has_many()
	{
		foreach ($this->has_many['definition'] as $alias => $values)
		{
			$options = array();

			$this->add_meta($alias, $options);
			// First fetch all the avaliable options
			$opts = array();

			$query = ORM::factory($this->has_many['definition'][$alias]['model']);
			$this->add_options($query, $options);

			if (empty($options['driver']))
			{
				// If the driver hasn't already been specified, specify it
				$options['driver'] = (isset($this->config->drivers['has_many']))
					? $this->config->drivers['has_many']
					: 'checkboxes';
			}

			$this->form->add($alias, $options);
		}
	}
	
	/**
	 * Add options for relational fields (checkboxes, radios, select options)
	 * 
	 * @access protected
	 * @param mixed ORM $query
	 * @param mixed array & $options
	 * @return void
	 */
	protected function add_options(ORM $query, array & $options)
	{
		// Create the array
		$opts = array();
		foreach ($query->find_all() as $row)
		{
			$primary_key = $row->primary_key();
			$primary_val = $row->primary_val();
			
			$opts[$row->{$primary_val}] = $row->{$primary_key};
		}
		
		// Add the options to the field
		$options['options'] = $opts;
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
		// Then load the validate definitions
		$this->rules = $this->model->rules();
		$this->labels = $this->model->labels();

		// Pull out relationship data
		foreach (self::$relationship_types as $type)
		{
			$this->{$type}['definition'] = $this->model->$type();

			foreach ($this->{$type}['definition'] as $key => $values)
			{
				$value = (isset($values['far_key']))
					? $values['far_key']
					: $values['foreign_key'];
				
				$this->{$type}['foreign_keys'][$value] = $key;
			}
		}

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
