<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Validator_Field class.
 *
 * @package   Formo
 * @category  Validator
 */
abstract class Formo_Core_Validator_Field extends Formo_Container {

	/**
	 * Validation instance used for actual from values
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_validation;

	/**
	 * Return the field error or set the error
	 * 
	 * @access public
	 * @param mixed $message. (default: NULL)
	 * @param mixed array $params. (default: NULL)
	 * @return void
	 */
	public function error($message = NULL, array $params = NULL)
	{
		if (func_num_args() !== 0)
		{
			$field = $this->alias();
			$this->_validation()->label($this->alias(), $this->view()->label());
			$this->_validation()->error($field, $message, $params);
		}

		$errors = $this->errors();

		return Arr::get($errors, $this->alias());
	}
	
	/**
	 * Determine which message file to use
	 *
	 * @access public
	 * @return string
	 */
	public function message_file()
	{
		// Always return the parent's message file if this one's isn't defined
		return $this->get('message_file')
			? $this->get('message_file')
			: $this->parent()->message_file();
	}
	
	/**
	 * Run validation on a single field
	 * 
	 * @access public
	 * @param mixed $require_sent. (default: FALSE)
	 * @return void
	 */
	public function validate($require_sent = TRUE)
	{
		$this->_validation();
		$this->driver()->pre_validate();
		$this->_add_rules();
		
		$passed = $this->_validation()->check();
		
		$this->_run_callbacks($passed);

		return $passed;
	}
	
	/**
	 * Add rules and labels to local validation object
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _add_rules(Validation $validation = NULL)
	{
		$validation = ($validation === NULL)
			? $this->_validation()
			: $validation;

		$validation->label($this->alias(), $this->view()->label());
		$validation->rules($this->alias(), $this->get('rules'));
	}
	
	/**
	 * Return validator errors
	 * 
	 * @access public
	 * @return void
	 */
	public function errors($file = NULL, $translate = TRUE)
	{
		$file = $this->_get_message_file($file);

		$errors = $this->_validation()->errors($file, $translate);

		return $errors;
	}
	
	protected function _validation()
	{
		if (empty($this->_validation))
		{
			// Fetch form values if there's a parent
			$array = ($parent = $this->parent())
				? $parent->as_array('value')
				: array();

			$this->_validation = Validation::factory($array)
				->bind(':form', $this->parent());
		}

		return $this->_validation;
	}
	
	/**
	 * Return a validation object with copied rules, labels, etc
	 * 
	 * @access public
	 * @param mixed $value. (default: NULL)
	 * @return void
	 */
	public function validation($value = NULL)
	{
		$this->driver()->pre_validate();

		$array = (func_num_args())
			? array($this->alias() => $value)
			: array();

		$validation = new Validation($array);
		$this->_add_rules($validation);

		return $validation;
	}
	
	protected function _get_message_file($file)
	{
		if ($file === NULL)
		{
			$file = Formo::config($this, 'message_file');
		}
		
		return $file;
	}
	
	/**
	 * Add a callback
	 * 
	 * @access public
	 * @param mixed $type
	 * @param mixed $method
	 * @param mixed array $values. (default: NULL)
	 * @return void
	 */
	public function callback($type, $method, array $values = NULL)
	{
		$this->_defaults['callbacks'][$type][] = array($method, $values);
		
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
		
		foreach ($types as $type)
		{
			if ($callbacks = arr::get($callbacks, $type))
			{
				$this->_defaults[$type] += $callbacks;
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
			$method = array_shift($callbacks);
			$values = arr::get($callbacks, 0, array());

			$this->_replace_callback_vals(':self', $values);
			call_user_func_array($method, $values);
		}
	}

}
