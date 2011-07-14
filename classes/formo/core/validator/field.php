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

		return $this->_validation()->check();
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
			$this->_validation = new Validation(array($this->alias() => $this->val()));
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
			// First check for fiel-specific message_file
			$file = $this->get('message_file');
			
			if ($file === NULL)
			{
				// Then look for parent message_file
				$file = $this->parent()->get('message_file');
			}
			
			if ($file === NULL)
			{
				// Finally default on ocnfig default
				$file = Kohana::$config->load('formo')->message_file;
			}
		}
		
		return $file;
	}

}
