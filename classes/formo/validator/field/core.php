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

		$errors = $this->parent()->errors('validate');

		return Arr::get($errors, $this->alias());
	}

}
