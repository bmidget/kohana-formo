<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Validator_Field class.
 *
 * @package   Formo
 * @category  Validator
 */
abstract class Formo_Core_Validator_Field extends Formo_Container {

	protected $_validation;

	public function error($message = NULL, array $params = NULL)
	{
		if (func_num_args() !== 0)
			return $this->parent()->error($this->alias(), $message, $params);

		$errors = $this->parent()->errors();

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
	public function validate($value = NULL)
	{
		$this->_validation();
		$this->driver()->pre_validate();
		
		$value = (func_num_args())
			? $value
			: $this->val();
		
		$vals = array($this->alias() => $value);
		$this->_validation = $this->_validation->copy($vals);

		return $this->_validation->check();
	}
	
	/**
	 * Return validator errors
	 * 
	 * @access public
	 * @return void
	 */
	public function errors($file = NULL, $translate = TRUE)
	{
		if ($file === NULL)
		{
			$file = $this->parent()->message_file();
		}

		$errors = $this->_validation->errors($file, $translate);
		$error = Arr::get($errors, $this->alias());

		return Arr::get($errors, $this->alias());
	}
	
	protected function _validation()
	{
		if ( ! empty($this->_validation))
		{
			return $this->_validation;
		}
		
		$this->_validation = new Validation(array());
		$this->_validation->rules($this->alias(), $this->get('rules'));
	}

}
