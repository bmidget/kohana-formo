<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Validator_Filter_Core class.
 * 
 * @package  Formo
 */
class Formo_Validator_Filter_Core extends Formo_Validator_Item {

	public $type = 'filter';
	
	public static function factory($callback, array $args = NULL)
	{		
		return new Formo_Validator_Filter($callback, $args);
	}

	public function __construct($callback, array $args = NULL)
	{
		$this->callback = $callback;
		$this->args = (array) $args;
	}
	
	public function execute()
	{
		$callback = $this->context
			? array($this->context, $this->callback)
			: $this->callback;
		
		// Set the context's value to whatever the filter returns
		return call_user_func_array($callback, $this->args);
	}

}