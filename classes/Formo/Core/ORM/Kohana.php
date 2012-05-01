<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_ORM_Kohana_Core class. This is the driver for the official Kohana 3.1.x ORM module
 *
 * @package   Formo
 * @category  Decorators
 */
abstract class Formo_Core_ORM_Kohana extends Formo_ORM {

	/**
	 * A parent form or subform
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_form;

	/**
	 * The model associated with the form
	 *
	 * @var mixed
	 * @access public
	 */
	public $model;

	/**
	 * Config definition from forno_kohana.php
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_config;

	/**
	 * The validation object from the model
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_validation;

	/**
	 * Names of relationship fields
	 *
	 * (default value: array('has_many', 'belongs_to', 'has_one'))
	 *
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $_relationship_types = array('has_many', 'belongs_to', 'has_one');

	/**
	 * Tracks field names that have relationships
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_relational_fields = array();

	/**
	 * Has One relationships from model
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_has_one = array
	(
		'definitions' => array(),
		'foreign_keys' => array(),
	);

	/**
	 * Belongs To relationships from model
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_belongs_to = array
	(
		'definitions' => array(),
		'foreign_keys' => array(),
	);

	/**
	 * Has Many relationships from model
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_has_many = array
	(
		'definitions' => array(),
		'foreign_keys' => array(),
	);

	/**
	 * Rules definitions from model
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_rules = array();

	/**
	 * Label definitions from model
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_labels = array();

	/**
	 * $_formo array from model
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_formo = array();

	/**
	 * Fields to use
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_fields = array();

	/**
	 * Fields to skip
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_skip_fields = array();

	/**
	 * Keeps track of field relationships for unloaded records because of K3's ORM limitation
	 * that doesn't allow adding/removing relationships from unloaded records
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_habtm_relationships = array();

	/**
	 * Keeps track of field relationships for unloaded records because of K3's ORM limitation
	 * that doesn't allow add()/remove() on non-habtml fields
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_has_many_relationships = array();

	/**
	 * Keeps track of field relationships for unloaded records because of K3's ORM limitation
	 * that doesn't allow add()/remove() on non-habtml fields
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_has_one_relationships = array();

	/**
	 * Track whether pre_render has been run
	 *
	 * (default value: FALSE)
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_pre_render_run = FALSE;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $form
	 * @return void
	 */
	public function __construct($form)
	{
		$this->_form = $form;
	}

	/**
	 * Load fields into the form
	 *
	 * @access public
	 * @param mixed ORM $model
	 * @param mixed array $fields. (default: NULL)
	 * @return void
	 */
	public function load(ORM $model, array $fields = NULL, $skip_fields = FALSE)
	{
		$this->model = $model;
		$this->_config = Kohana::$config->load('formo_kohana');
		$this->_make_fields($fields, $skip_fields);
		$this->_load_meta();
		
		if ($arr = arr::get($this->_formo, ':self'))
		{
			foreach ($arr as $var => $vals)
			{
				// Add :self stuff
				$this->_form->set($var, $vals);
			}
		}

		foreach ($model->as_array() as $alias => $value)
		{
			if ($this->_use_field($alias) === FALSE)
				// If the field is supposed to be skipped, ignore it altogether
				continue;

			// The bool that tracks whether the field is relational
			$relational_field = FALSE;
			// Create the array
			$options = array();
			// The default is the value from the table
			$options['value'] = $this->model->$alias;
			// If the field is a belongs_to field, do some extra processing
			$foreign_key = $this->_process_belongs_to($alias, $options);
			// Add meta data for the field
			$this->_add_meta($alias, $options, $foreign_key);

			if (empty($options['driver']))
			{
				// Default to the default driver
				$options['driver'] = $this->_config()->drivers['default'];
			}

			$this->_form
				->add($alias, $options);
		}

		$this->_add_has_relationships();

		return $this->_form;
	}

	/**
	 * Add meta data defined in model's $_formo array to applicable fields
	 *
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return void
	 */
	protected function _add_meta($alias, array & $options, $foreign_key = NULL)
	{
		$alias = ( ! empty($options['alias']))
			? $options['alias']
			: $alias;

		$opts = array();
		if ($settings = Arr::get($this->_formo, $alias))
		{
			// First find formo settings for the field
			$opts = $settings;
		}

		if ($rules = Arr::get($this->_rules, $alias))
		{
			// Then add rules to the options
			$opts['rules'] = $rules;
		}

		if ($foreign_key !== NULL AND $rules = Arr::get($this->_rules, $foreign_key))
		{
			// Attach foreign key rules
			if ( ! empty($opts['rules']))
			{
				$opts['rules'] = array_merge($rules, $opts['rules']);
			}
			else
			{
				$opts['rules'] = $rules;
			}
		}

		$options = array_merge($options, $opts);
	}

	/**
	 * Add the correct alias if it isn't already defined
	 *
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return string
	 */
	protected function _add_alias($alias, array & $options)
	{
		if (empty($options['alias']))
		{
			$options['alias'] = $alias;
		}

		return $options['alias'];
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
		// Empty values should be NULL
		($value === '' AND $value = NULL);

		$alias = $field->alias();
		$in_model = TRUE;

		if (
				! array_key_exists($alias, $this->model->as_array())
				AND ! isset($this->_belongs_to['definitions'][$alias])
				AND ! isset($this->_has_many['definitions'][$alias])
			)
			// Don't add fields that aren't in the model
			return;

		// First check is has_many
		if ($definitions = Arr::get($this->_has_many['definitions'], $alias) AND $definitions['through'] === NULL)
		{
			$this->_has_many_relationship($alias, $value);
			return;
		}
		// Then habtm
		elseif ($definitions)
		{
			// Remove any relationships that have been removed
			foreach ($this->model->$alias->find_all() as $row)
			{
				$primary_key = $row->primary_key();
				if ( ! in_array($primary_key, $value))
				{
					$this->_habtm_relationship($alias, 'remove', $row);
				}
			}

			foreach ($value as $_value)
			{
				$record = ORM::factory($definitions['model'], $_value);
				$this->_habtm_relationship($alias, 'add', $record);
			}

			return;
		}

		if ($definitions = Arr::get($this->_has_one['definitions'], $alias))
		{
			$record = $this->model->$alias;

			if ($record->pk() != $value)
			{
				$this->_has_one_relationship($alias, $value);
			}

			return;
		}

		if ($definitions = Arr::get($this->_belongs_to['definitions'], $alias))
		{
			$field = $definitions['foreign_key'];
			$this->model->$field = $value;

			return;
		}

		// By default, simply set the value of the field to the form field value
		$this->model->$alias = $value;
	}

	/**
	 * Add/remove relationship to many-to-many fields. This is because ORM currently
	 * does not support adding relationships to unloaded records
	 *
	 * @access protected
	 * @return void
	 */
	protected function _habtm_relationship($alias, $method, $value)
	{
		// Save relationship changes to be run after save() method
		$this->_habtm_relationships[] = array
		(
			'alias'  => $alias,
			'method' => $method,
			'value'  => $value,
		);
	}

	/**
	 * Add has_many relationahips to track
	 *
	 * @access protected
	 * @param mixed $alias
	 * @param mixed $values
	 * @return void
	 */
	protected function _has_many_relationship($alias, $values)
	{
		// Save relationship changes to be run after save() method
		$this->_has_many_relationships[] = array
		(
			'alias' => $alias,
			'value' => $values,
		);
	}

	/**
	 * Add has_one relationships to track
	 *
	 * @access protected
	 * @param mixed $alias
	 * @param mixed $value
	 * @return void
	 */
	protected function _has_one_relationship($alias, $value)
	{
		// Save relationship changes to be run after save() method
		$this->_has_one_relationships[] = array
		(
			'alias'  => $alias,
			'value'  => $value,
		);
	}

	/**
	 * Add relational data to belongs_to fields
	 *
	 * @access protected
	 * @param mixed $alias
	 * @param mixed array & $options
	 * @return void
	 */
	protected function _process_belongs_to($alias, array & $options)
	{
		if ( ! isset($this->_belongs_to['foreign_keys'][$alias]))
			// No need to process non-belongs-to fields
			return NULL;

		$foreign_key = $alias;

		// The alias in the model for the field
		$field_alias = $this->_belongs_to['foreign_keys'][$alias];

		// Add the alias if it wasn't already explicitly defined
		$_alias = $this->_add_alias($field_alias, $options);

		if (empty($options['driver']))
		{
			// If the driver hasn't already been specified, specify it
			$options['driver'] = (isset($this->_config->drivers['belongs_to']))
				? $this->_config->drivers['belongs_to']
				: 'select';
		}

		// Add to relational_fields array
		$this->_relational_fields[$_alias] = ORM::factory($this->_belongs_to['definitions'][$field_alias]['model']);

		// Also determine the value
		if ( ! isset($options['value']))
		{
			$options['value'] = $this->model->$alias;
		}

		return $foreign_key;
	}

	/**
	 * Add has_one relationships to form
	 *
	 * @access protected
	 * @return void
	 */
	protected function _add_has_one()
	{
		foreach ($this->_has_one['definitions'] as $alias => $value)
		{
			$options = array();

			$this->_add_meta($alias, $options);
			$_alias = $this->_add_alias($alias, $options);

			// Add relational fields array
			$this->_relational_fields[$_alias] = ORM::factory($this->_has_one['definitions'][$alias]['model']);
		}
	}

	/**
	 * Add has_many and has_one relationships to form
	 *
	 * @access protected
	 * @return void
	 */
	protected function _add_has_relationships()
	{
		foreach (array('has_many', 'has_one') as $type)
		{
			$_type = "_$type";
			foreach ($this->{$_type}['definitions'] as $alias => $value)
			{
				if ($this->_use_field($alias) === FALSE)
					// Only use the correct fields
					continue;

				$options = array();

				$this->_add_meta($alias, $options);
				$_alias = $this->_add_alias($alias, $options);

				// Add to relational fields array
				$this->_relational_fields[$_alias] = ORM::factory($this->{$_type}['definitions'][$alias]['model']);

				if (empty($options['driver']))
				{
					$options['driver'] = Arr::get($this->_config->drivers, $type, 'checkboxes');
				}

				$this->_form->add($alias, $options);
			}
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
	protected function _add_options($alias, ORM $query, array & $options)
	{
		// First check to see if there are any query options to limit the records
		if ($limit = $this->_form->$alias->get('records'))
		{
			$query = $limit($query);
		}

		// Create the array
		$opts = array();
		foreach ($query->find_all() as $row)
		{
			$primary_key = $row->primary_key();

			$primary_val = Formo::config($this->_form->$alias, 'orm_primary_val');

			// Use the primary value
			$opts[$row->{$primary_key}] = $row->{$primary_val};
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
	protected function _field_type($alias)
	{
		foreach (self::$_relationship_types as $type)
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
	protected function _make_fields(array $fields = NULL, $skip_fields = FALSE)
	{
		if ($fields === NULL)
			// Only do anything if fields are specified
			return;

		if ($skip_fields === TRUE)
		{
			// If the skip_field flag is set, fields are fields to skip
			return $this->_skip_fields = $fields;
		}
		else
		{
			// Otherwise fields are a list of fields to use
			return $this->_fields = $fields;
		}
	}
	
	/**
	 * Determine if a field should be included
	 * 
	 * @access protected
	 * @param mixed $alias
	 * @return bool
	 */
	protected function _use_field($alias)
	{
		if (in_array($alias, $this->_skip_fields))
			// If a field has been specified to skip, don't use it
			return FALSE;
		
		if ( ! empty($this->_fields))
		{
			if ( ! in_array($alias, $this->_fields))
				// The field has to be specifically named to be included
				// if this->_fields is set
				return FALSE;
		}
		
		// Use the field by default
		return TRUE;
	}

	/**
	 * Load validate and relationship data for easy access
	 *
	 * @access protected
	 * @return void
	 */
	protected function _load_meta()
	{
		// Pull out relationship data
		foreach (self::$_relationship_types as $type)
		{
			$_type = "_$type";
			$this->{$_type}['definitions'] = $this->model->$type();

			foreach ($this->{$_type}['definitions'] as $key => $values)
			{
				$value = (isset($values['far_key']))
					? $values['far_key']
					: $values['foreign_key'];

				$this->{$_type}['foreign_keys'][$value] = $key;
			}
		}

		$this->_rules = $this->model->rules();

		if (is_callable(array($this->model, 'formo')))
		{
			// The formo meta data
			$this->_formo = $this->model->formo();
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
	protected function _determine_driver(array $options, $class)
	{
		// If the driver has been explicitly defined, use that
		if ( ! empty($options['driver']))
			return $options['driver'];


		// Check to find a default driver for a Jelly Field
		return ( ! empty($this->_config()->drivers[$class]))
			// Return the default driver
			? $this->_config()->drivers[$class]
			// Otherwise return the genral form default driver
			: $this->_form->get('config')->default_driver;
	}

	/**
	 * Run add(), remove() on all many-to-many relationships. This is intended to be
	 * run after saving the model
	 *
	 * @access public
	 * @return void
	 */
	public function save_rel()
	{
		foreach ($this->_habtm_relationships as $values)
		{
			$this->model->{$values['method']}($values['alias'], $values['value']);
		}

		foreach ($this->_has_many_relationships as $values)
		{
			$alias = $values['alias'];
			$foreign_key = $this->_has_many['definitions'][$alias]['foreign_key'];
			$model = $this->_has_many['definitions'][$alias]['model'];
			$primary_key = ORM::factory($model)->primary_key();

			$table_name = ORM::factory($model)->table_name();

			// Remove the appropriate records
			$remove_query = DB::update($table_name)
				->set(array($foreign_key => NULL))
				->where($foreign_key, '=', $this->model->pk());

			if ($values['value'])
			{
				// Add the applicable fields if there is a value
				$remove_query->where($primary_key, 'NOT IN', (array) $values['value']);

				// Add the appropriate records
				$add_query = DB::update($table_name)
					->set(array($foreign_key => $this->model->pk()))
					->where($primary_key, 'IN', (array) $values['value'])
					->execute();
			}

			$remove_query->execute();
		}

		foreach ($this->_has_one_relationships as $values)
		{
			$alias = $values['alias'];
			$value = $values['value'];

			$foreign_key = $this->_has_one['definitions'][$alias]['foreign_key'];
			$model = $this->_has_one['definitions'][$alias]['model'];

			if ($this->model->$alias->pk() != $value)
			{
				$this->model->$alias->$foreign_key = NULL;
				$this->model->$alias->save();

				$record = ORM::factory($model, $value);
				$record->$foreign_key = $this->model->pk();
				$record->save();
			}
		}
	}

	/**
	 * Fills relational fields with relation options to choose from
	 *
	 * @access public
	 * @return void
	 */
	public function pre_render()
	{
		if ($this->_pre_render_run === TRUE)
			// Don't run this method more than once per form
			return;

		foreach ($this->_relational_fields as $alias => $query)
		{
			if ($this->_form->$alias->get('render') === FALSE OR $this->_form->$alias->get('ignore') === TRUE)
				// Dont' load fields if the field is ignored or not rendered
				continue;

			$options = array();
			$this->_add_options($alias, $query, $options);

			// Set the options in the field object
			$this->_form->$alias->set('options', $options['options']);

			// Determine values for has_many relationships at pre_render time
			if ($this->_form->$alias->val_isset() === FALSE AND $definitions = Arr::get($this->_has_many['definitions'], $alias))
			{
				$values = array();
				foreach ($this->model->$alias->find_all() as $row)
				{
					$primary_key = $row->primary_key();
					// Add the value
					$values[] = $row->$primary_key;
				}

				$this->_form->$alias->val($values);
			}

			if ($this->_form->$alias->val_isset() === FALSE AND $definitions = Arr::get($this->_has_one['definitions'], $alias))
			{
				$this->_form->$alias->val($this->model->$alias->pk());
			}
		}

		// Track that this method has been run
		$this->_pre_render_run = TRUE;
	}
}
