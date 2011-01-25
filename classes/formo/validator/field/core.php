<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Validator_Field class.
 *
 * @package  Formo
 */
abstract class Formo_Validator_Field_Core extends Formo_Container {

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

}
