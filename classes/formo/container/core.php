<?php defined('SYSPATH') or die('No direct script access.');

// This class simply makes storing and retrieving objects neat
abstract class Formo_Container_Core {

	// The topmost parent
	const PARENT = '__PARENT';
	// Used to determine if new_value is set
	const NOTSET = '__UNSET';

	// Other settings
	protected $_settings = array();
	// Where custom vars are stored
	protected $_customs = array();
	
	// Container settings
	protected $_defaults = array
	(
		'alias'				=> NULL,
		'parent'			=> NULL,
		'fields'			=> array(),
		'driver_instance'	=> NULL,
	);
				
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
	
	// Fetch a field directly within its container
	public function field($search, $option = FALSE)
	{			
		if (is_array($search))
		{
			$fields = array();
			foreach ($search as $_search)
			{
				$field = $this->field($_search);
				$fields[$field->alias()] = $field;
			}
			
			return $fields;
		}
		
		// If $search is an int, search by key
		$find_by = is_string($search) ? 'item' : 'key';
		
		foreach ($this->_defaults['fields'] as $key => $field)
		{
			if ($find_by == 'key' AND $key === $search)
				return $field;
			
			if ($find_by == 'item' AND $search == $field->alias())
				return $field;
				
			if ($option AND $find_by == 'item' AND call_user_func($option, $search) == $field->alias())
				return $field;
		}
	}
	
	// Fetch a field or return a driver object
	public function __get($alias)
	{
		if ($alias == 'driver' AND $driver = $this->get('driver'))
		{									
			// Make sure the current driver is the correct driver
			if ($instance = $this->get('driver_instance') AND Formo_Driver_Factory::is_driver($instance, $driver))
				return $instance;
			
			// Create a new driver instance
			$instance = Formo_Driver_Factory::factory($this, $driver);
									
			// Store the driver instance
			$this->set('driver_instance', $instance);

			return $instance;
		}

		return $this->field($alias);
	}
	
	// Runs the method through the driver
	public function __call($func, $args)
	{
		$method = new ReflectionMethod($this->driver, $func);
		return $method->invokeArgs($this->driver, $args);
	}

	// Allow setting of variables with __set
	public function __set($variable, $value)
	{
		$this->set($variable, $value);
	}
	
	// Set variables
	public function set($variable, $value)
	{
		// Support array of key => values
		if (is_array($variable))
		{
			foreach ($variable as $_variable => $_value)
			{
				$this->set($_variable, $_value);
			}
			
			return $this;
		}
		
		// Allow the driver to alter the variable beforehand
		// but obviously it can't happen when setting the driver instance
		if ($variable != 'driver_instance' AND method_exists($this->driver, 'set_'.$variable))
		{
			$value = $this->driver->{'set_'.$variable}($value);
		}
				
		if (array_key_exists($variable, $this->_defaults))
		{
			$this->_defaults[$variable] = $value;
			return $this;
		}
		
		if (array_key_exists($variable, $this->_settings))
		{
			// First look for variables in $_settings
			$this->_settings[$variable] = $value;
			return $this;
		}
		
		// Otherwise just set the variable
		$this->$variable = $value;
		return $this;
	}
		
	// Fetch variables
	public function get($variable, $default = FALSE)
	{
		$arrays = array('_defaults', '_settings', '_customs');
		
		foreach ($arrays as $array)
		{
			if (array_key_exists($variable, $this->$array))
			{
				return $this->{$array}[$variable];
			}
		}
		
		// Return default if variable doesn't exist
		return (isset($this->$variable)) ? $this->$variable : $default;
	}
				
	// Return the model
	public function model()
	{
		if ($this instanceof Formo)
			return $this->get('model');
		
		return $this->parent()->get('model');
	}
	
	// Stores an item in the container
	public function append($field)
	{
		// Set the field's parent
		$field->set('parent', $this);
		$this->_defaults['fields'][] = $field;

		// Look for order and process it for ordering this field
		if ($field->get('order') !== FALSE)
		{
			$order = $field->get('order');
			$args = array($field);
			if (is_array($order))
			{
				$args[] = key($order);
				$args[] = current($order);
			}
			else
			{
				$args[] = $order;
			}
			
			call_user_func_array(array($this, 'order'), $args);
		}
		
		return $this;
	}
	
	// Add a field to the beginning of the form
	public function prepend($item)
	{
		$item->_defaults['parent'] = $this;
		array_unshift($this->_defaults['fields'], $item);
	}
	
	// Removes a field
	public function remove($alias)
	{
		// Support an array of fields
		if (is_array($alias))
		{
			foreach ($alias as $_alias)
			{
				$this->remove($_alias);
			}
			
			return $this;
		}
		
		foreach ($this->_defaults['fields'] as $key => $item)
		{
			if ($item->alias() == $alias)
			{
				unset($this->_defaults['fields'][$key]);
			}
		}
		
		return $this;
	}

	// Load construct options
	public function load_options($option, $value = NULL)
	{
		// Support array of options
		if (is_array($option))
		{
			foreach ($option as $_option => $value)
			{
				$this->load_options($_option, $value);
			}
			
			return $this;
		}
		
		// Otherwise just set the variable
		$this->set($option, $value);
		
		return $this;
	}

	// Return array of element with its specified value
	public function as_array($value = NULL)
	{
		// Create the empty array to fill
		$array = array();
		foreach ($this->_defaults['fields'] as $field)
		{
			$alias = $field->alias();
			
			// Make concession for grabbing 'value' as that one characteristic
			if ($value == 'value')
			{
				$array[$alias] = $field->val();
				continue;
			}
			
			// By default, return name => element
			$array[$alias] = ($value !== NULL)
				? $field->get($value)
				: $field;
		}
		
		return $array;
	}
	
	// Retrieve's an item's parent
	public function parent($search = NULL)
	{
		$this_parent = $this->_defaults['parent'];
				
		// If not searching, return this parent
		if ($search === NULL)
			return $this_parent;
		
		// If searching for the topmost parent, return it if this is
		if ($search === self::PARENT AND ! $this_parent)
			return $this;
		
		// If this parent doesn't exist, return FALSE
		if ( ! $this_parent)
			return FALSE;
		
		// If the parent's alias matches the search term
		if ($this_parent AND $this_parent->alias() == $search)
			return $this_parent;
		
		// Recursively search for the correct parent
		return $this_parent->parent($search);			
	}
	
	// Convenience method for fetching/setting alias
	public function alias($alias = NULL)
	{
		if (func_num_args() == 0)
			return $this->_defaults['alias'];

		$this->_defaults['alias'] = $alias;
		return $this;
	}
			
	// Look through a form object for a formo or formo_field objeect
	// by alias
	public function find($alias)
	{
		// If an array wasn't entered, look everywhere
		if ( ! is_array($alias))
		{
			// Whenever a match is found, return it
			if ($field = $this->field($alias))
				return $field;
			
			// Recursively look as deep as necessary
			foreach ($this->_defaults['fields'] as $field)
			{
				if ($found_field = $field->find($alias))
					return $found_field;
			}
		}
		// If an array was entered, follow the exact path of the array
		else
		{
			// Start with the first item
			$field = $this->field($alias[0]);
			// Go deeper for each item entered
			for($i=1; $i<count($alias); $i++)
			{
				$field = $field->field($alias[$i]);
			}	
			
			return $field;
		}
	}
	
	// Return the order a field is in
	protected function find_order($search)
	{
		$i = 0;
		foreach ($this->_defaults['fields'] as $field)
		{
			// Return the order if we just found it
			if ($field->alias() == $search)
				return $i;
				
			$i++;
		}
		
		// Return false upon failing
		return FALSE;
	}
	
	// Get the key of a specific field
	protected function find_fieldkey($field)
	{
		foreach ($this->_defaults['fields'] as $key => $value)
		{
			if ($value->alias() == $field)
				return $key;
		}

		return FALSE;		
	}
	
	// Set the order of a new field
	public function order($field, $new_order, $relative_field = NULL)
	{
		// Find the field if necessary
		$field = (is_object($field) === FALSE) ? $this->find($field) : $field;
		
		// Pull out all the fields
		$fields = $field->parent()->get('fields');
		
		// Delete the current place
		unset($fields[$this->find_fieldkey($field->alias())]);
		
		// If the new order is a string, it's a comparative order
		if ( ! ctype_digit($new_order) AND is_string($new_order))
		{
			$position = $this->find_order($relative_field);
			
			// If the place wasn't found, do nothing
			if ($position === FALSE)
				return $this;
				
			$new_order = ($new_order == 'after') ? $position + 1 : $position;
		}

		// Make the insertion
		array_splice($fields, $new_order, 0, array($field));
		// Save the new order
		$field->parent()->set('fields', $fields);
		
		return $this;
	}
		
}