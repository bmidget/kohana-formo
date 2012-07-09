<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_ORM_Kohana {

	public static function load( array $array)
	{
		$model = $array['model'];
		$field = $array['field'];

		foreach ($model->as_array() as $alias => $value)
		{
			// The bool that tracks whether the field is relational
			$relational_field = FALSE;
			// Create the array
			$options = array('alias' => $alias);
			// The default is the value from the table
			$options['value'] = $model->$alias;
			// If the field is a belongs_to field, do some extra processing
			//$foreign_key = $this->_process_belongs_to($alias, $options);
			// Add meta data for the field
			//$this->_add_meta($alias, $options, $foreign_key);

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

}