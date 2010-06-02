<?php defined('SYSPATH') or die('No direct script access.');

abstract class Validator_Core extends Container {

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
	public function validate($values = NULL)
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}
						
		if ($values != TRUE
			AND (method_exists($this, 'sent') AND ! $this->sent())
			AND ! $this->parent(Container::PARENT)->sent())
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
				$rule->execute();

				if (isset($rule->error) AND $rule->error !== FALSE)
				{
					// Set this error
					$this->error($rule->error, TRUE, self::param_names($rule));

					// Do not continue if there was an error
					break;
				}				
			}
		}
		
		// Validate through each of the field's fields
		foreach ($this->defaults('fields') as $field)
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
		
		// Find the setting for whether to throw exceptions or simply return FALSE
		$throw_exception = Arr::get($this->get('config', array()), 'throw_exceptions', FALSE);
				
		// What to return depends on if it's a field or form object
		if ($this instanceof Formo)
		{
			// If the form/subform has an error message, return FALSE
			if ($this->error() !== FALSE)
			{
				throw new Validate_Exception($this->errors());
				return FALSE;
			}

			// Otherwise return whether the form/subform has no errors
			$passed = (bool) $this->errors() === FALSE;
			
			if ($passed === FALSE)
				throw new Validate_Exception($this->errors());

			return $passed;
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
		$context->add_validator('filters', Filter::factory($context, $callback, $args));
		
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
		$context->add_validator('post_filters', Filter::factory($field, $context, $callback, $args));
		
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
		$this->make_context($context, $callback);
		
		// Add the rule
		$field->add_validator('rules', Rule::factory($field, $context, $callback, $args));
		
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
	public function trigger($field, Trigger $trigger)
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
	
	protected function make_context( & $context, & $callback)
	{
		$regex = '/:([a-zA-Z_0-9]+)::/';
		
		// Determine the context of the rule
		if (preg_match($regex, $callback, $matches))
		{
			switch ($matches[1])
			{
				case 'parent':
					$context = $this->parent();
					break;
				case 'form':
					$context = $this->parent(Container::PARENT);
					break;
				case 'model':
					$context = $this->model();
					break;
			}
			
			$callback = preg_replace($regex, '', $callback);
		}
		elseif (preg_match('/::/', $callback))
		{
			$context = NULL;
		}
		elseif (function_exists($callback))
		{
			$context = NULL;
		}
		elseif ($model = $this->model() AND method_exists($model, $callback))
		{
			$context = $model;
		}
		elseif (method_exists($this, $callback))
		{
			$context = $this;
		}
		elseif (is_callable(array('Validate', $callback)))
		{
			$callback = 'Validate::'.$callback;
			$context = NULL;
		}
	}
	
	public static function param_names($rule)
	{
		// Make the array
		$array = array();
		
		$i = 1;
		foreach ($rule->args as $pretty_name => $arg)
		{
			$next = ':param'.$i;
			if (is_string($pretty_name))
			{
				// If the key is a string, use it as a pretty name
				$array[$next] = $pretty_name;
				
				$i++;
				continue;
			}
			
			// Use a Container object's alias
			$array[$next] = ($arg instanceof Container)
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
		
		$values = Arr::merge(array(':field' => $this->alias()), (array) $param_names);
		
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
			$new_params[$keyh] = ($this instanceof Formo)
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
		return (bool) $this->val();
	}
	
	// return whether a field is checked
	public function is_checked()
	{
		return (bool) $this->val();
	}
	
	public function matches($match_against)
	{
		return $this->val() === $this->parent(Container::PARENT)
			->find($match_against)
			->val();
	}

	/*	Built-in Triggers	*/
	public function validated()
	{
		return (bool) $this->error() === FALSE;
	}

}