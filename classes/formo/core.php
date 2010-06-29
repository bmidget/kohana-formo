<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core {
	
	const NOTSET = '__NOTSET';
	const PARENT = '__PARENT';
	
	// Return a form object
	public static function form($alias = NULL, $driver = NULL, array $options = NULL)
	{
		return new Formo_Form($alias, $driver, $options);
	}
	
	// Return a field object
	public static function field($alias, $driver = NULL, $value = NULL, array $options = NULL)
	{
		return new Formo_Field($alias, $driver, $value, $options);
	}
	
	// For radios, checkboxes, select, etc.
	public static function group($alias, $driver, $options, $values, array $settings = NULL)
	{
		$settings['values'] = $values;
		$settings['options'] = $options;
		$settings['driver'] = $driver;
		$settings['alias'] = $alias;
		
		return new Formo_Field($settings);
	}
	
	// Return a new render object
	public static function render_obj($type, $options)
	{
		$class = Kohana::config('formo')->render_classes[$type];
		return new $class($options);
	}
	
	// Return a new rule object
	public static function rule()
	{
		$args = func_get_args();
		
		$method = new ReflectionMethod('Formo_Validator_Rule', 'factory');
		return $method->invokeArgs(NULL, $args);
	}
	
	// Return a new trigger object
	public static function trigger()
	{
	
	}
	
	// Return a new filter object
	public static function filter()
	{
		$args = func_get_args();
			
		$method = new ReflectionMethod('Formo_Validator_Filter', 'factory');
		return $method->invokeArgs(NULL, $args);
	}
	
	// Return a new filter object
	public static function display_filter()
	{
		$args = func_get_args();
		
		$method = new ReflectionMethod('Formo_Validator_Filter', 'factory');
		$filter = $method->invokeArgs(NULL, $args);
		$filter->type = 'display_filter';
		
		return $filter;
	}
		
	// Return or create a new driver instance
	public function load_driver($save_instance = FALSE)
	{
		// Fetch the current settings
		$driver = $this->get('driver');
		$instance = $this->get('driver_instance');
		
		// If the instance is the correct driver for the field, return it
		if ($instance AND get_class($instance) == $driver)
			return $instance;
		
		// Build the class name
		$driver_class_name = Kohana::config('formo')->driver_prefix.ucfirst($driver);
				
		// Create the new instance
		$instance = new $driver_class_name($this);
		
		if ($save_instance === TRUE)
		{
			// Save the instance if asked to
			$this->set('driver_instance', $instance);
		}
		
		// Return the new driver instance
		return $instance;
	}
		
	public function load_orm($save_instance = FALSE)
	{
		if ( ! $this instanceof Formo_Form)
			return $this->parent()->load_orm(TRUE);
			
		if ($instance = $this->get('orm_driver_instance'))
			// If the instance exists, return it
			return $instance;
		
		// Get the driver neame
		$driver = Kohana::config('formo')->orm_driver;
		
		// Create the new instance
		$instance = new $driver($this);
		
		if ($save_instance === TRUE)
		{
			// Save the instance if asked to
			$this->set('orm_driver_instance', $instance);
		}
		
		// REturn the new orm driver instance
		return $instance;
	}
				
	// Simplifies taking function arguments
	// Turn all arguments into one nice $options array
	public static function args($class, $method, $args)
	{
		$method = new ReflectionMethod($class, $method);
		
		$options = array();
 		$original_options = array();
				
		$i = 0;
		foreach ($method->getParameters() as $param)
		{
			if ( ! isset($args[$i]))
				continue;
																		
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
	
	public static function notset($val, & $var = NULL)
	{
		$var = $val;
		return $val === Formo::NOTSET;
	}
	
	// Load construct options
	public function load_options($option, $value = NULL)
	{
		// Support array of options
		if (is_array($option))
		{
			foreach ($option as $_option => $_value)
			{
				$this->load_options($_option, $_value);
			}
			
			return $this;
		}
		
		// Otherwise just set the variable
		$this->set($option, $value);
		
		return $this;
	}	

}