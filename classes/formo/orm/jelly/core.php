<?php defined('SYSPATH') or die('No direct script access.');

class Formo_ORM_Jelly_Core {

	// Default mapping of field types to drivers
	protected static $driver_map = array
	(
		'default'	=> 'text',
		'text'		=> 'text',
		
	);
	
	// Custom driver definitions
	protected static $custom_driver_map = array();
	
	// Reset stuff, then load the model
	public static function select( & $model, Formo $form, array $data)
	{
		return Jelly::select($data[0], $data[1]);
	}
	
	// Load fields from jelly model
	public static function load(Jelly_Model $model, Formo $form, array $fields = NULL)
	{
		$skip_fields = array();
		if ($fields AND in_array('*', $fields))
		{
			$skip_fields = $fields;
			$fields = NULL;
		}

		foreach ($model->meta()->fields() as $column => $field)
		{
			if (in_array($column, $skip_fields))
				continue;
			
			if ($fields AND ( ! in_array($column, $fields)))
				continue;
							
			$options = (array) $field + array('value' => $model->get($column));
			
			// Look for validation rules as defined by the config file
			foreach (Kohana::config('formo_jelly')->validation_keys as $key => $value)
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
			
//			echo Kohana::debug($options);
			
			// NOTE: This shouldn't really happen until pre_render
/*
			$options = array('value' => $model->get($column));
			
			$add_options = array('driver', 'rules', 'filters', 'triggers', 'post_filters');
			
			foreach ($add_options as $option)
			{
				if ( ! empty($field->$option))
				{
					$options[$option] = $field->$option;
				}
			}
*/			
			$form->add($column, $options);
		}
	}

	public static function group(Jelly_Model $model, Formo $form, $group = 'default')
	{
		if (is_array($group))
		{
			foreach ($group as $_group)
			{
				self::group($model, $form, $_group);
			}

			return;
		}

		self::load($model, $form, $model->_groups[$group]);
	}

	protected static function map(array $settings)
	{
		foreach ($settings as $field_type => $driver)
		{
			self::$custom_driver_map[$field_type] = $driver;
		}
	}
	
	public static function set(Jelly_Model $model, Formo $form, array $data = NULL)
	{
		$model->set($form->as_array('_value'));
	}


}