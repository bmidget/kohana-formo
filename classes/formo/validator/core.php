<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_Validator_Core class.
 *
 * @package   Formo
 * @category  Validator
 */
abstract class Formo_Validator_Core extends Formo_Container {

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
	protected function setup_validation()
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
			call_user_func_array(array($this->_validation, $method), $args);

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
	public function validation()
	{
		return $this->_validation;
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
	public function validate($require_sent = FALSE)
	{
		if ($require_sent === TRUE AND $this->sent() === FALSE)
			// If form wasn't sent, and sent is required, doesn't pass validation
			return FALSE;

		// Tracks if there were errors in any subforms
		$subform_errors = FALSE;
		// Tracks if there were any errors inside this form
		$errors = FALSE;

		// Build the array
		$array = array();

		foreach ($this->fields() as $field)
		{
			if ($field->get('render') === FALSE OR $field->get('ignore') === TRUE)
				continue;

			// Add the rules
			$this->add_rules($field);

			if ($field instanceof Formo_Form)
			{
				if ( ! $field->validate($require_sent))
				{
					$subform_errors = TRUE;
				}

				continue;
			}
			else
			{
				$array[$field->alias()] = $field->val();
			}
		}

		// Add rules for this form as well
		$this->add_rules();

		$array[$this->alias()] = $this->val();
		
		if ($driver = $this->orm_driver())
		{
			// Bind :model to the model
			$this->_validation->bind(':model', $driver->model);
		}

		$this->_validation = $this->_validation->copy($array);

		$errors = $this->determine_errors();
		
		return ($subform_errors === FALSE)
			? $errors === FALSE
			: FALSE;
	}

	/**
	 * Add rules to the validation object
	 *
	 * @access protected
	 * @param mixed Formo_Container $field. (default: NULL)
	 * @return void
	 */
	protected function add_rules(Formo_Container $field = NULL)
	{
		$obj = ($field !== NULL)
			? $field
			: $this;

		if ( ! $rules = $obj->get('rules'))
			// Only do anything if the field has rules
			return;

		$this->validation()->label($obj->alias(), $obj->alias());
		$this->validation()->rules($obj->alias(), $rules);
	}
	
	/**
	 * Make sure existing errors carry over from validation
	 * 
	 * @access protected
	 * @param mixed $errors
	 * @param mixed array $existing_errors
	 * @return boolean
	 */
	protected function determine_errors()
	{
		$existing_errors = $this->_validation->errors();
		$errors = $this->_validation->check() === FALSE;
		
		if (empty($existing_errors))
			// If there weren't any errors predefined before validation, return check() result
			return $errors;
		
		foreach ($existing_errors as $key => $values)
		{
			$this->_validation->error($key, current($values));
		}
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
		$this->val_field($field)->merge('rules', $rules);

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
		$this->val_field($field)->merge('rules', $new_rule);

		return $this;
	}

	/**
	 * Return the correct field for adding validation
	 *
	 * @access protected
	 * @param mixed $field
	 * @return void
	 */
	protected function val_field($field)
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
	public function error($field = NULL, $message = NULL, array $params = NULL)
	{
		$this->_validation->label($field, $field);

		$num_args = func_num_args();
		if ($num_args !== 0)
		{
			if ($num_args === 1)
			{
				$field = '_form';
			}

			$this->_validation->error($field, $message, $params);
			return $this;
		}

		return Arr::get($this->validation()->errors(), 'form');
	}

	/**
	 * Convenience method for setting and retrieving error
	 *
	 * @access public
	 * @param mixed array $errors. (default: NULL)
	 * @return object or string
	 */
	public function errors($file = NULL, $translate = TRUE)
	{
		if (func_num_args() === 0)
		{
			$file = $this->message_file();
		}

		return $this->_validation->errors($file, $translate);
	}

	// Determine which message file to use
	protected function message_file()
	{
		return $this->get('message_file')
			? $this->get('message_file')
			: Kohana::config('formo')->message_file;
	}

}
