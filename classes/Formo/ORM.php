<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This trait gives Formo functionality directly to your models
 * Intended to be mixed in with Kohana ORM Models
 */
trait Formo_ORM {

	/**
	 * Optional formo alias for form
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_formo_alias;

	/**
	 * Used to easily find foreign keys by field name
	 * 
	 * (default value: [])
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_foreign_keys = [];

	/**
	 * Create a form from an ORM Model
	 * You can optionally pass a form to have fields added to
	 * 
	 * @access public
	 * @param array $fields (default: NULL)
	 * @param Formo $form (default: NULL)
	 * @return Formo
	 */
	public function get_form( array $fields, Formo $form = NULL)
	{
		if (Arr::get($fields, 0) === '*')
		{
			// Get the default set of fields
			// By default, load all the fields
			$arr = array_keys($this->as_array());

			if (count($fields) > 1)
			{
				// If other fields are listed, add them to the array
				// this allows for something like ['*', 'foo', 'bar']
				array_shift($fields);
				$arr = Arr::merge($arr, $fields);
			}

			// Set the fields to the combined array
			$fields = $arr;
		}

		if ($form === NULL)
		{
			// If a form object wasn't passed as the second argument,
			// create that form object here
			$alias = $this->_formo_alias ?: $this->_object_name;
			$form = Formo::form(['alias' => $alias]);
		}

		// Set up foreign keys map for easy access
		$this->_find_foreign_keys();

		// Now we iterate through each field
		foreach ($fields as $key => $field_name)
		{
			if (is_array($field_name))
			{
				$method = 'formo_'.$key;

				if (strpos($method, '.') !== FALSE)
				{
					$method = str_replace('.', '_', $method);
				}

				$rs = $this->$method();

				if ($rs instanceof Database_Result)
				{
					$model = $rs->current();
					$blueprint = $model->get_form($field_name)
						->set('alias', $key)
						->set('blueprint', true)
						->pad_blueprint($rs);

					$form->add($blueprint);
					continue;
				}
				elseif ($rs instanceof ORM)
				{
					$subform = $rs->get_form($field_name);
					$form->add($subform);
					continue;
				}
			}

			if ($this->_field_exists($field_name) !== TRUE)
			{
				// Custom method looks like _formo_$field_name()
				$method = '_formo_'.$field_name;
				if ( ! method_exists($this, $method))
				{
					// Throw an exception if a field is requested but there's no method defining its options array
					throw new Kohana_Exception('Formo custom field method, :method, does not exist.', [':method' => __CLASS__.'::'.$method.'()']);
				}

				$options = $this->$method();
			}
			else
			{
				// Create the field definition array
				$options = [
					'alias' => $field_name,
					'val' => $this->$field_name,
					'driver' => 'input',
				];

				// Do any special processing if the field is a relational field
				$this->_process_belongs_to($field_name, $options);
				$this->_process_has_one($field_name, $options);
				$this->_process_enum($field_name, $options);
				$this->_process_set($field_name, $options);
			}

			// Add the field to the form
			$form->add($options);
		}

		if ($form->config('model_base_rules') === TRUE)
		{
			// If form is set up to include basic MySQL mimicking validation rules
			// then figure them out
			$rules = $this->_get_base_rules();
		}

		// Add Model defined rules to base rules
		$rules = (isset($rules)) ? Arr::merge($rules, $this->rules()) : $this->rules();

		if ($filters = $this->filters())
		{
			foreach ($filters as $alias => $_filters)
			{
				// Add filters as well
				$form->merge($alias, ['filters' => $_filters]);
			}
		}

		// Add the rules to their respective fields
		$form->add_rules_fields($rules);

		$this->formo($form);

		return $form;
	}

	/**
	 * Post-production after get_form
	 * 
	 * @access protected
	 * @param mixed $form
	 * @return void
	 */
	protected function formo($form)
	{
		/*
		Add this function to any models you want to
		do any model-spcific Formo stuff such as 
		any defaults definitions for your fields

		For example:
		
		$form->set_fields([
			'id' => [
				'render' => false,
			],
			'email' => [
				'driver' => 'input|email',
			],
		]);
		*/
	}

	/**
	 * Utility method that just creates a array suitable for Formo opts from
	 * a result set
	 * 
	 * @access public
	 * @static
	 * @param mixed $result
	 * @param mixed $key
	 * @param mixed $value
	 * @return array
	 */
	public static function select_list($result, $key, $value)
	{
		$array = [];
		foreach ($result as $row)
		{
			$array[$row->$key] = $row->$value;
		}

		return $array;
	}

	/**
	 * Convenience method for fetching the field name of a foreign model
	 * Formo allows 
	 * 
	 * @access protected
	 * @static
	 * @param ORM $model
	 * @return string
	 */
	protected static function _get_field_name( ORM $model)
	{
		$field_name = ( ! empty($model->_primary_val))
			? $model->_primary_val
			: 'name';

		return $field_name;
	}

	protected function _field_exists($column)
	{
		return (array_key_exists($column, $this->_object) OR
			array_key_exists($column, $this->_related) OR
			array_key_exists($column, $this->_has_one) OR
			array_key_exists($column, $this->_belongs_to) OR
			array_key_exists($column, $this->_has_many));
	}

	/**
	 * Fills $this->_foreign_keys in order to easier
	 * find foreign keys in all the process_type methods
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _find_foreign_keys()
	{
		if ( ! empty($this->_foreign_keys))
		{
			// Only process foreign keys once
			return;
		}

		// Pull out relationship data
		foreach (['has_many', 'belongs_to', 'has_one'] as $type)
		{
			// The foreign keys are prepended with "_"
			$var = '_'.$type;

			foreach ($this->$var as $field_name => $val)
			{
				// Se the foreign key as the key and the field name as the value
				$key = Arr::get($val, 'far_key') ?: $val['foreign_key'];
				$this->_foreign_keys[$type][$key] = $field_name;
			}
		}
	}

	/**
	 * Apply rules based on mysql field definitions
	 * While you can skip these, it's a good idea to include them in your fields
	 * 
	 * @access protected
	 * @return array
	 */
	protected function _get_base_rules()
	{
		$rules = [];
		foreach ($this->_table_columns as $alias => $data)
		{
			if ($data['is_nullable'] !== TRUE AND ! (in_array(Arr::get($data, 'data_type'), ['set', 'tinyint'])))
			{
				// Non nullable fields get the not_empty rule
				$rules[$alias][] = ['not_empty'];
			}

			if ($data['type'] === 'int' AND $data['data_type'] !== 'tinyint')
			{
				// Add digit and range rules to int types
				$rules[$alias][] = ['digit', [':value', true]];
				$rules[$alias][] = ['range', [':value', Arr::get($data, 'min', 0), Arr::get($data, 'max', 1)]];
			}
			elseif ($data['type'] === 'varchar')
			{
				// Varchars have max lenghts, so add the maxlenght rule
				$rules[$alias][] = ['maxlength', [':value', Arr::get($data, 'character_maximum_length')]];
			}
		}

		return $rules;
	}

	/**
	 * Add options to belongs_to fields
	 * 
	 * @access protected
	 * @param mixed $field_name
	 * @param array & $options
	 * @return void
	 */
	protected function _process_belongs_to($field_name, array & $options)
	{
		$field_alias = Arr::path($this->_foreign_keys, 'belongs_to.'.$field_name, []);

		if ( ! $field_alias)
		{
			// No need to process non-belongs-to fields here
			return NULL;
		}

		if (Arr::get($this->_belongs_to[$field_alias], 'formo') === TRUE)
		{
			$foreign_model = static::factory($this->_belongs_to[$field_alias]['model']);
			$opts = $foreign_model->find_all();

			$options['driver'] = 'select';
			$options['opts'] = static::select_list($opts, $foreign_model->primary_key(), static::_get_field_name($foreign_model));
		}
	}

	/**
	 * Set up options for has_one relationships
	 * 
	 * @access protected
	 * @param mixed $field_name
	 * @param array & $options
	 * @return void
	 */
	protected function _process_has_one($field_name, array & $options)
	{
		$field_name = Arr::path($this->_foreign_keys, 'has_many.'.$field_name, []);

		if ( ! $field_name)
		{
			// No need to process non-belongs-to fields here
			return NULL;
		}

		if (Arr::get($this->_has_one[$field_name], 'formo') === TRUE)
		{
			$options['driver'] = 'select';
			$foreign_model = ORM::factory($this->_has_one[$field_name]['model']);
			$opts = $foreign_model->find_all();
			$options['opts'] = static::select_list($opts, $foreign_model->primary_key(), $this->_get_field_name($foreign_model));
		}
	}

	/**
	 * Set up options for enum fields
	 * 
	 * @access public
	 * @param mixed $field_name
	 * @param array & $options
	 * @return void
	 */
	protected function _process_enum($field_name, array & $options)
	{
		$column = Arr::get($this->_table_columns, $field_name, []);

		if (Arr::get($column, 'data_type') !== 'enum')
		{
			// If the field isn't an enum, skip this stuff
			return;
		}

		$opts = Arr::get($column, 'options', []);

		$options['driver'] = 'select';
		$options['opts'] = array_combine($opts, $opts);

		if (empty($options['val']) AND $this->loaded() === FALSE)
		{
			$options['val'] = Arr::get($column, 'column_default');
		}
	}

	/**
	 * Set up options for set fields
	 * 
	 * @access public
	 * @param mixed $field_name
	 * @param array & $options
	 * @return void
	 */
	protected function _process_set($field_name, array & $options)
	{
		$column = Arr::get($this->_table_columns, $field_name, []);

		if (Arr::get($column, 'data_type') !== 'set')
		{
			return;
		}

		$opts = Arr::get($column, 'options', []);
		$options['driver'] = 'set';
		$options['opts'] = array_combine($opts, $opts);

		if (empty($options['val']) AND $this->loaded() === FALSE)
		{
			$column_default = Arr::get($column, 'column_default', '');
			$options['val'] = ( ! empty($column_default))
				? explode(',', $column_default)
				: [];
		}
		elseif (isset($options['val']) AND is_string($options['val']))
		{
			$options['val'] = explode(',', $options['val']);
		}
	}

}