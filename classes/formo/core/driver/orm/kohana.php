<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_ORM_Kohana {

protected static $_relationship_types = array('has_many', 'belongs_to', 'has_one');

	public static function load( array $array)
	{
		$model = $array['model'];
		$field = $array['field'];
		$std = new stdClass;

		static::_build_relationships($model, $std);

		foreach ($model->as_array() as $alias => $value)
		{
			// The bool that tracks whether the field is relational
			$relational_field = FALSE;
			// Create the array
			$options = array('alias' => $alias);
			// The default is the value from the table
			$options['val'] = $model->$alias;
			// If the field is a belongs_to field, do some extra processing
			static::_process_belongs_to($alias, $model, $std, $options);
			//$foreign_key = $this->_process_belongs_to($alias, $options);
			// Add meta data for the field

			if (empty($options['driver']))
			{
				// Default to the default driver
				$options['driver'] = 'input';
			}

			$field
				->add($options);
		}

		if ($rules = $model->rules())
		{
			foreach ($rules as $alias => $_rules)
			{
				$field->rules($alias, $_rules);
			}
		}

		if (method_exists($model, 'formo'))
		{
			$model->formo($field);
		}

		//$this->_add_has_relationships();
	}

	public static function select_list($result, $key, $value)
	{
		$array = array();
		foreach ($result as $row)
		{
			$array[$row->$key] = $row->$value;
		}

		return $array;
	}

	protected static function _build_relationships( Kohana_ORM $model, stdClass $std)
	{
		// Pull out relationship data
		foreach (static::$_relationship_types as $type)
		{
			$std->{$type} = array
			(
				'definitions' => array(),
				'foreign_keys' => array(),
			);

			$std->{$type}['definitions'] = $model->$type();

			foreach ($std->{$type}['definitions'] as $key => $values)
			{
				$value = (isset($values['far_key']))
					? $values['far_key']
					: $values['foreign_key'];

				$std->{$type}['foreign_keys'][$value] = $key;
			}
		}
	}

	protected static function _process_belongs_to($alias, Kohana_ORM $model, stdClass $std, array & $options)
	{
		if ( ! isset($std->belongs_to['foreign_keys'][$alias]))
		{
			// No need to process non-belongs-to fields
			return NULL;
		}

		$field_alias = $std->belongs_to['foreign_keys'][$alias];

		$options['driver'] = 'select';
		
		$opts = ORM::factory($std->belongs_to['definitions'][$field_alias]['model'])->find_all();
		$options['opts'] = static::select_list($opts, 'id', 'name');
	}

}