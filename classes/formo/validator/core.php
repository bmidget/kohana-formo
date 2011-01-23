<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_Validator_Core class.
 *
 * @package  Formo
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
		$this->_validation = new Validation(array());
		$this->_validation->label('_form', '_form');
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
			// Run validation methods inside the validation object
			return call_user_func_array(array($this->_validation, $method), $args);

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

		$array[$this->alias()] = $this->val();

		$this->_validation = $this->_validation->copy($array);
		$errors = $this->_validation->check() === FALSE;

		return ($subform_errors === FALSE)
			? $errors === FALSE
			: FALSE;
	}

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
	 * Deterine whether data was sent
	 *
	 * @access public
	 * @param mixed array $input. (default: NULL)
	 * @return void
	 */
	public function sent(array $input = NULL)
	{
		$input = ($input !== NULL) ? $input : $this->get('input');

		foreach ((array) $input as $alias => $value)
		{
			if ($this->find($alias) !== TRUE)
				return TRUE;
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

		return Arr::get($this->validation()->errors('validate'), 'form');
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
		return $this->_validation->errors($file, $translate);
	}

}
