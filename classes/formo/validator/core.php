<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Validator_Core extends Formo_Container {

	protected $_errors = array
	(
		// Error message for this field/form
		'error'		=> FALSE,
		// Error messages for fields inside
		'errors'	=> array(),
	);

	protected $_validators = array
	(
		// Pre filters
		'filters'		=> array(),
		// Normal validation rules
		'rules'			=> array(),
		// Special triggers
		'triggers'		=> array(),
		// Filter prior to rendering
		'post_filters'	=> array(),
	);
	
	// Convenience method for setting and retrieving error
	public function error($message = NULL, $translate = FALSE, array $param_names = NULL)
	{
		if (func_num_args() > 0)
		{
			($translate AND $message = $this->make_message($message, $param_names));
			return $this->_errors['error'] = $message;
		}
		
		return $this->_errors['error'];
	}

	// Convenience method for fetching/setting errors
	public function errors(array $errors = NULL)
	{
		if (func_num_args() == 1 AND $errors !== NULL)
			return $this->_errors['errors'] = $errors;
			
		return $this->_errors['errors'];
	}

	// Add a validator item
	public function add_validator($type, $rule, $alias = NULL)
	{
		// Allow giving a rule an alias
		$next = ($alias !== NULL) ? $alias : count($this->_validators[$type]);
						
		$this->_validators[$type][$next] = $rule;
		
		return $this;
	}

	// Retrieve a validator set
	public function get_validator($type)
	{
		return $this->_validators[$type] ? $this->_validators[$type] : array();
	}
	
	// Remove a validator item by alias
	public function remove_validator($type, $alias)
	{
		unset($this->_validators[$type][$alias]);
		
		return $this;
	}
	
	// Run validation
	public function validate($validate_if_not_sent = FALSE)
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}
		
		$this->driver->pre_validate();
		
		if ($validate_if_not_sent === FALSE
			AND (method_exists($this, 'sent') AND ! $this->sent())
			AND ! $this->parent(Formo_Container::PARENT)->sent())
			return FALSE;
			
		if ($this->error() !== FALSE)
			return FALSE;

		// Run through each validator type in order
		foreach ($this->_validators as $name => $rules)
		{
			// Don't do post_filters right now
			if ($name == 'post_filters')
				continue;
							
			// Execute each of the rules
			foreach ($rules as $rule)
			{
				// Run the rule
				if ($rule->execute() === FALSE)
				{
					// Set this error
					$this->error($rule->error, TRUE, self::param_names($rule));
					// No need to continue if there was an error
					break;
				}
			}
		}
		
		// Validate through each of the field's fields
		foreach ($this->get('fields') as $field)
		{
			// Don't do anything if it's ignored
			if ($field->get('ignore') === TRUE)
				continue;
												
			// Validate everything else
			if ($field->validate() === FALSE)
			{
				if ($field instanceof Formo)
				{
					// If no errors are attached to the subform, continue
					if ( ! $field_errors = $field->errors())
						continue;
					
					// Attach subform errors to the parent's errors
					$this->errors(Arr::merge($this->errors(), array($field->alias() => $field->errors())));					
				}
				else
				{
					// Attach field's error to its parent
					$this->errors(Arr::merge($this->errors(), array($field->alias() => $field->error())));
				}
			}
		}

		if (isset($benchmark))
		{
			// Stop benchmarking
			Profiler::stop($benchmark);
		}
		
		$this->driver->post_validate();
				
		// What to return depends on if it's a field or form object
		if ($this instanceof Formo)
		{
			// If the form/subform has an error message, return FALSE
			if ($this->error() !== FALSE)	
				return FALSE;

			// Otherwise return whether the form/subform has no errorss
			return (bool) $this->errors() === FALSE;
		}
		else
		{				
			// Return whether passed validation based on field's error
			return (bool) $this->error() === FALSE;
		}
	}
	
	public function filter($field, $callback, array $args = NULL)
	{
		$context = $field === NULL ? $this : $this->find($field);
		// Add the filter
		$context->add_validator('filters', Formo_Filter::factory($context, $callback, $args));
		
		return $this;
	}
	
	// Allow inputting multiple filters
	public function filters(array $array)
	{
		foreach ($array as $options)
		{
			call_user_func_array(array($this, 'filter'), $options);
		}
		
		return $this;
	}
		
	public function post_filter($field, $callback, array $args = NULL)
	{
		$context = $field === NULL ? $this : $this->find($field);
		
		// Add the post filter
		$context->add_validator('post_filters', Formo_Filter::factory($field, $context, $callback, $args));
		
		return $this;
	}

	// Allow inputting multiple post filters
	public function post_filters(array $array)
	{
		foreach ($array as $options)
		{
			call_user_func_array(array($this, 'post_filter'), $options);
		}
		
		return $this;
	}
			
	// Add a rule to an item in the container object
	public function rule($field, $callback, array $args = NULL)
	{
		$context = $field = ($field === NULL) ? $this : $this->find($field);
		$this->make_context($context, $callback, $args);
		
		// Add the rule
		$field->add_validator('rules', Formo_Rule::factory($field, $context, $callback, $args));
		
		return $this;
	}
	
	// Allow inputting multiple rules
	public function rules(array $array)
	{
		foreach ($array as $options)
		{
			call_user_func_array(array($this, 'rule'), $options);
		}
		
		return $this;
	}

	// Add a trigger to an item in the container object
	public function trigger($field, Formo_Trigger $trigger)
	{
		// Allow the context to be this field
		$context = $field !== NULL ? $this->find($field) : $this;
		// Set the context of the trigger
		$trigger->context($context);
		// Add the trigger to the bunch
		$context->add_validator('triggers', $trigger);
		
		return $this;
	}
	
	// Allow inputting multiple triggers
	public function triggers(array $array)
	{
		foreach ($array as $options)
		{
			call_user_func_array(array($this, 'trigger'), $options);
		}
		
		return $this;
	}
	
	// Determine the proper context for the rule to run against
	protected function make_context( & $context, & $callback, & $args = array())
	{
		$regex = '/:([a-zA-Z_0-9]+)::/';
		
		// Pseudo context is present
		if (preg_match($regex, $callback, $matches))
		{
			switch ($matches[1])
			{
				case 'parent':
					$context = $this->parent();
					break;
				case 'form':
					$context = $this->parent(Formo_Container::PARENT);
					break;
				case 'model':
					$context = $this->model();
					break;
			}
			
			// Set the callback to the second part of the rule
			return $callback = preg_replace($regex, '', $callback);			
		}
		
		if (preg_match('/::/', $callback))
			// Separate the context from the callback
			return list($context, $callback) = explode('::', $callback);
			
		if (function_exists($callback))
			// Set context to NULL if it's a stand-alone function
			return $context = NULL;
			
		if (method_exists($this, $callback))
			// Allow simple declarations of field rules
			return;
			
		if (is_callable(array('Validate', $callback)))
		{
			// Set the context to Validate
			$context = 'Validate';

			// Check to see if backwards compatibility for validate is set
			if (Kohana::config('formo')->validate_compatible === TRUE)
			{
				$args = (array) $args;
				if ( ! in_array(':value', $args))
				{
					array_unshift($args, ':value');
				}
			}
			
			return;
		}				
	}
	
	public static function param_names($rule)
	{
		// Make the array
		$array = array();		
		
		$i = 0;
		foreach ($rule->args as $pretty_name => $arg)
		{
			if ($i === 0 AND Kohana::config('formo')->validate_compatible === TRUE
				AND ! (is_object($rule->context))
				AND $rule->context == 'Validate')
			{
				$i++;
				continue;
			}
			
			($i === 0 AND $i++);
				
			$next = ':param'.$i;
			if (is_string($pretty_name))
			{
				// If the key is a string, use it as a pretty name
				$array[$next] = $pretty_name;
				
				$i++;
				continue;
			}
			
			// Use a Container object's alias
			$array[$next] = ($arg instanceof Formo_Container)
				? $arg->alias()
				: $arg;
				
			$i++;
		}
		
		return $array;
	}
	
	public function make_message($error_name, array $param_names = NULL)
	{
		$file = Kohana::config('formo')->validate_messages_file;
		
		if ($message = Kohana::message($file, $this->alias().'.'.$error_name))
		{
			// Found a message for this field and error
		}
		elseif ($message = Kohana::message($file, $this->alias().'.default'))
		{
			// Found a default message for this field
		}
		elseif ($message = Kohana::message('validate', $error_name))
		{
			// Found a default message for this error
		}
		else
		{
			// No message exists, display the path expected
			$message = $file.'.'.$this->alias().'.'.$error_name;
		}
		
		$values = Arr::merge(array(':value' => $this->val(), ':field' => $this->alias()), (array) $param_names);
		
		$message = strtr($message, $values);
		
		if (Kohana::config('formo')->translate === TRUE)
		{
			$values[':field'] = __($values[':field']);
			$message = __($message, $values);
		}
		
		return $message;
	}
	
	/*	Built-in Validation Rules	*/
	
	// Replace pseudo_params
	public function pseudo_args( & $params)
	{
		$new_params = $params;

		if (($key = array_search(':field', $params)) !== FALSE)
		{
			$new_params[$key] = $this;
		}
		
		if (($key = array_search(':alias', $params)) !== FALSE)
		{
			$new_params[$key] = $this->alias();
		}
		
		if (($key = array_search(':parent', $params)) !== FALSE)
		{
			$new_params[$key] = $this->parent();
		}

		if (($key = array_search(':form', $params)) !== FALSE)
		{
			$new_params[$key] = $this->parent(Container::PARENT);
		}

		if (($key = array_search(':model', $params)) !== FALSE)
		{
			$new_params[$key] = ($this instanceof Formo)
				? $this->get('model')
				: $this->parent()->get('model');
		}
		
		if (($key = array_search(':value', $params)) !== FALSE)
		{
			$new_params[$key] = $this->val();
		}
		
		$params = empty($new_params) ? array($this->val()) : $new_params;
		
		return $params;
	}
	
	public function not_empty()
	{
		$new_value = $this->get('new_value');
		
		if ($new_value === Formo_Container::NOTSET AND ! $this->get('value'))
			return FALSE;
			
		return (bool) $new_value;
	}
		
	public function matches($match_against)
	{
		return $this->val() === $this->parent(Formo_Container::PARENT)
			->find($match_against)
			->val();
	}

	/*	Built-in Triggers	*/
	public function validated()
	{
		return (bool) $this->error() === FALSE;
	}

}