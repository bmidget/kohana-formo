<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_Validator_Core class.
 *
 * @package   Formo
 * @category  Validator
 */
abstract class Formo_Core_Validator extends Formo_Container {

	/**
	 * A validation object for a form
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_validation;

	/**
	 * Track all errors
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_errors = array
	(
		'form' => array(),
		'fields' => array(),
	);
	
	/**
	 * Create validation object
	 *
	 * @access protected
	 * @return void
	 */
	protected function _setup_validation()
	{
		$this->_validation = Validation::factory(array())
			->bind(':form', $this);
	}

	/**
	 * Add ability to run methods against validation object
	 *
	 * @access public
	 * @param mixed $method
	 * @param mixed $args
	 * @return void
	 */
	public function __call($method, $args)
	{
		if (is_callable(array($this->_validation, $method)))
		{
			// Run validation methods inside the validation object
			$method = new ReflectionMethod($this->_validation, $method);
			$method->invokeArgs($this->_validation, $args);

			return $this;
		}

		return parent::__call($method, $args);
	}

	/**
	 * Access validation object
	 *
	 * @access public
	 * @return void
	 */
	public function validation(array $values = NULL)
	{
		if (func_num_args() === 0)
		{
			$values = $this->as_array('value');
		}

		$validation = new Validation($values);
		$this->_add_rules($validation);

		return $validation;
	}

	/**
	 * Check a series of values against form's validation rules
	 *
	 * @access public
	 * @param mixed array $array. (default: NULL)
	 * @return void
	 */
	public function check(array $array = NULL)
	{
		return $this->_validate->check($array);
	}

	/**
	 * Validate forms, fields and whether form has been sent
	 *
	 * @access public
	 * @param mixed array $array. (default: NULL)
	 * @return void
	 */
	public function validate($require_sent = TRUE)
	{
		if ($require_sent === TRUE AND $this->sent() === FALSE)
			// If form wasn't sent, and sent is required, doesn't pass validation
			return FALSE;

		$this->driver()->pre_validate();

		// Tracks if there were errors in any subforms
		$subform_errors = FALSE;
		// Tracks if there were any errors inside this form
		$has_errors = FALSE;
		// Keep all the error messages
		$error_messages = array();

		foreach ($this->fields() as $field)
		{
			if ($field->get('render') === FALSE OR $field->get('ignore') === TRUE)
				continue;

			if ( ! $field->validate($require_sent))
			{
				$has_errors = TRUE;
				$error_messages[$field->alias()] = $field->error();
			}
		}

		if ($has_errors === FALSE)
		{
			$this->_add_rules($this->_validation);
			// Add this value to the validation object
			$data = $this->_validation->data();
			$data += array($this->alias() => $this->val());
			$this->_validation = $this->_validation->copy($data);

			$has_errors = $this->_determine_errors() === FALSE;
		}
		
		$passed_validation = ($has_errors === FALSE);
		
		$this->_run_callbacks($passed_validation);

		return $passed_validation;
	}

	/**
	 * Add rules to the validation object
	 *
	 * @access protected
	 * @param mixed Formo_Container $field. (default: NULL)
	 * @return void
	 */
	protected function _add_rules(Validation $validation)
	{
		if ( ! $rules = $this->get('rules'))
			// Only do anything if the field has rules
			return;

		$validation->label($this->alias(), $this->view()->label());
		$validation->rules($this->alias(), $rules);
	}

	/**
	 * Make sure existing errors carry over from validation
	 *
	 * @access protected
	 * @param mixed $errors
	 * @param mixed array $existing_errors
	 * @return boolean
	 */
	protected function _determine_errors()
	{
		if ($this->_validation->errors())
			return FALSE;
		
		return $this->_validation->check();
	}

	/**
	 * Store multiple validation rules
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed array $rules
	 * @return object
	 */
	public function rules($field, array $rules)
	{
		$this->_val_field($field)->merge('rules', $rules);

		return $this;
	}

	/**
	 * Store a single validation rule
	 *
	 * @access public
	 * @param mixed $field
	 * @param mixed $rule
	 * @param mixed array $params. (default: NULL)
	 * @return object
	 */
	public function rule($field, $rule, array $params = NULL)
	{
		$new_rule = array(array($rule, $params));
		$this->_val_field($field)->merge('rules', $new_rule);

		return $this;
	}

	/**
	 * Return the correct field for adding validation
	 *
	 * @access protected
	 * @param mixed $field
	 * @return void
	 */
	protected function _val_field($field)
	{
		if ($field instanceof Formo_Container)
			return $field;

		if ($field == $this->alias())
			return $this;

		return $field !== NULL
			? $this->$field
			: $this;
	}

	/**
	 * Deterine whether data was sent
	 *
	 * @access public
	 * @param mixed array $input. (default: NULL)
	 * @return void
	 */
	public function sent(array $input = NULL)
	{
		$input = ($input !== NULL) ? $input : $this->get('input');

		if (empty($input))
			return FALSE;

		foreach ($input as $alias => $value)
		{
			if ($this->find($alias) !== NULL)
				return TRUE;

			// Check against a namespace
			if (is_array($value))
			{
				if ($this->alias() == $alias OR $this->find($alias) !== NULL)
				{
					foreach ($value as $_alias => $_value)
					{
						if ($this->find($_alias) !== NULL)
							return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

	/**
	 * Convenience method for setting and retrieving error
	 *
	 * @access public
	 * @param mixed $message. (default: NULL)
	 * @param mixed $translate. (default: FALSE)
	 * @param mixed array $param_names. (default: NULL)
	 * @return object or string
	 */
	public function error($field_alias = NULL, $message = NULL, array $params = NULL)
	{
		$num_args = func_num_args();
		if ($num_args !== 0)
		{
			if ($num_args === 1)
			{
				$message = $field_alias;
				$params = (array) $message;
				$field_alias = $this->alias();

				$this->_validation->label($this->alias(), $this->view()->label());
				$this->_validation->error($this->alias(), $message, $params);
			}
			else
			{
				$this->$field_alias->error($message, $params);
			}

			return $this;
		}

		return Arr::get($this->errors(), $this->alias());
	}

	/**
	 * Retrieve error messages
	 *
	 * @access public
	 * @param mixed array $errors. (default: NULL)
	 * @return object or string
	 */
	public function errors($file = NULL, $translate = TRUE)
	{
		$errors = array();
		foreach ($this->fields() as $field)
		{
			$errors += $field->errors($file, $translate);
		}
		
		// Add this error
		$file = $file
			? $file
			: Formo::config($this, 'message_file');

		$errors += $this->_validation->errors($file, $translate);

		return $errors;
	}

	/**
	 * Ensure the array for a field's callbacks exists and is an array
	 * 
	 * @access protected
	 * @param mixed $type
	 * @param mixed $alias
	 * @return void
	 */
	protected function _create_callback_array($type, $alias)
	{
		if ( ! isset($this->_defaults['callbacks'][$type][$alias]))
		{
			$this->_defaults['callbacks'][$type][$alias] = array();
		}
		
		return $this->_defaults['callbacks'][$type][$alias];
	}

	/**
	 * Add a callback
	 * 
	 * @access public
	 * @param mixed $type
	 * @param mixed $alias
	 * @param mixed $method
	 * @param mixed array $values. (default: NULL)
	 * @return void
	 */
	public function callback($type, $alias, $method, array $values = NULL)
	{
		$this->_create_callback_array($type, $alias);
		$this->_defaults['callbacks'][$type][$alias][] = array($method, $values);
		
		return $this;
	}
	
	/**
	 * Add multiple callbacks
	 * 
	 * @access public
	 * @param mixed array $callbacks
	 * @return void
	 */
	public function callbacks(array $callbacks)
	{
		$types = array('pass', 'fail');
		
		foreach ($callbacks as $type => $callback)
		{
			foreach ($callback as $alias => $_callbacks)
			{
				$this->_create_callback_array($type, $alias);
				$this->_defaults['callbacks'][$type][$alias] += $_callbacks;
			}
		}
		
		return $this;
	}

	/**
	 * Run callbacks
	 * 
	 * @access protected
	 * @param mixed $passed_validatoin
	 * @return void
	 */
	protected function _run_callbacks($passed_validatoin)
	{
		$type = ($passed_validatoin === TRUE) ? 'pass' : 'fail';
		$all_callbacks = arr::get($this->_defaults['callbacks'], $type, array());

		foreach ($all_callbacks as $alias => $callbacks)
		{
			foreach ($callbacks as $callback)
			{
				$method = array_shift($callback);
				$values = arr::get($callback, 0, array());
				
				$this->_replace_callback_vals($alias, $values);
				call_user_func_array($method, $values);
			}
		}
	}

	// Determine which message file to use
	public function message_file()
	{
		return Formo::config($this, 'message_file');
	}

	public static function range($field, $form)
	{
		$value = $form->$field->val();
		$max = $form->$field->attr('max');
		$min = $form->$field->attr('min');
		$step = $form->$field->attr('step');

		if ($min AND $value <= $min)
			return FALSE;

		if ($max AND $value >= $max)
			return FALSE;

		// Use the default step of 1
		( ! $step AND $step = 1);

		return strpos(($value - $min) / $step, '.') === FALSE;
	}

}
