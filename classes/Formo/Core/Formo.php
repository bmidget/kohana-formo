<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The interface class for Formo
 *
 * @package   Formo
 * @category  Interface
 */
class Formo_Core_Formo {

	/**
	 * An unset parameter
	 */
	const NOTSET = '__NOTSET';
	/**
	 * The topmost parent object
	 */
	const PARENT = '__PARENT';

	/**
	 * Return a form object
	 *
	 * @access public
	 * @static
	 * @param mixed $alias. (default: NULL)
	 * @param mixed $driver. (default: NULL)
	 * @param mixed array $options. (default: NULL)
	 * @return Formo_Form object
	 */
	public static function form($alias = NULL, $driver = NULL, array $options = NULL)
	{
		return new Formo_Form($alias, $driver, $options);
	}

	/**
	 * Return a field object
	 *
	 * @access public
	 * @static
	 * @param mixed $alias
	 * @param mixed $driver. (default: NULL)
	 * @param mixed $value. (default: NULL)
	 * @param mixed array $options. (default: NULL)
	 * @return Formo_Field object
	 */
	public static function field($alias, $driver = NULL, $value = NULL, array $options = NULL)
	{
		return new Formo_Field($alias, $driver, $value, $options);
	}

	/**
	 * For radios, checkboxes, select, etc.
	 *
	 * @access public
	 * @static
	 * @param mixed $alias
	 * @param mixed $driver
	 * @param mixed $options
	 * @param mixed $values
	 * @param mixed array $settings. (default: NULL)
	 * @return Formo_Field object
	 */
	public static function group($alias, $driver, $options, $values, array $settings = NULL)
	{
		$settings['values'] = $values;
		$settings['options'] = $options;
		$settings['driver'] = $driver;
		$settings['alias'] = $alias;

		return new Formo_Field($settings);
	}

	/**
	 * Simplifies taking function arguments
	 * Turns all arguments into one nice $options array
	 *
	 * @access public
	 * @static
	 * @param mixed $class
	 * @param mixed $method
	 * @param mixed $args
	 * @return array
	 */
	public static function args($class, $method, $args)
	{
		$method = new ReflectionMethod($class, $method);

		$options = array();
 		$original_options = array();

		$i = 0;
		foreach ($method->getParameters() as $param)
		{
			if ( ! isset($args[$i]))
			{
				$i++;
				continue;
			}

			$new_options = (is_array($args[$i]))
	            // If the arg was an array and the last param, use it as the set of options
				? $args[$i]
	            // If not, add it to the options by parameter name
				: array($param->name => $args[$i]);

	        $options = Arr::merge($options, $new_options);

			$i++;
		}

		return $options;
	}

	/**
	 * Determines whether a setting has been set
	 *
	 * @access public
	 * @static
	 * @param mixed $val
	 * @param mixed & $var. (default: NULL)
	 * @return bool
	 */
	public static function is_set($val, & $var = NULL)
	{
		$var = $val;
		return $val !== Formo::NOTSET;
	}

	/**
	 * Convert array values to a select_list
	 *
	 * @access public
	 * @static
	 * @param mixed array $array
	 * @return array
	 */
	public static function select_list(array $array)
	{
		$assoc_array = array();

		foreach ($array as $key => $value)
		{
			if ( ! is_array($value))
			{
				$assoc_array += array($key => $value);
			}
			else
			{
				$_value = array_values(array_pad($value, 2, NULL));
				$assoc_array[$_value[0]] = $_value[1];
			}
		}

		return $assoc_array;
	}
	
	public static function config($field, $config_item, $file = 'formo')
	{
		// Fist check for the field
		if ($value = $field->get($config_item, FALSE))
			return $value;
		
		// Next check against the parent
		if ($parent = $field->parent() AND $value = $parent->get($config_item, FALSE))
			return $value;
		
		// Finally, result to the Config file
		return Kohana::$config->load($file.'.'.$config_item);
	}

}
