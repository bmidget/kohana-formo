<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Validator_Rule_Core class.
 * 
 * @package  Formo
 */
class Formo_Validator_Rule_Core extends Formo_Validator_Item {

	public $type = 'rule';
	
	public static function factory($callback, array $args = NULL)
	{
		return new Formo_Validator_Rule($callback, $args);
	}
	
	public function __construct($callback, array $args = NULL)
	{
		$this->callback = $callback;
		$this->args = (array) $args;
	}
		
	protected static function make_name($callback)
	{
		// Return the function name, not the class namespace
		return preg_replace('/[:]*[a-zA-Z_0-9]+::/', '', $callback);
	}
	
	protected function error($result)
	{
		if ( ! is_bool($result))
			return;
		
		if ($result === TRUE)
			// Return TRUE if the rule passed
			return TRUE;
			
		// Set the error to the callback name
		$this->error = $this->callback;
		
		// Always return FALSE for an error
		return FALSE;
	}

	public function execute()
	{
		// Support for PHP < 5.3	
		$callback = ( strpos($this->callback, '::') !== FALSE )
			? explode('::', $this->callback)
			: $this->callback;
			
		if ((bool) $this->context === TRUE)
		{
			$method = new ReflectionMethod($this->context, $this->callback);
			$context = (is_object($this->context)) ? $this->context : NULL;
			
			// If the validate method returns false
			return $this->error($method->invokeArgs($context, array_values($this->args)));
		}		
		else
		{
			// Otherwise run the method as a standalone method
			return $this->error(call_user_func_array($callback, array_values($this->args)));
		}
		
		// Return TRUE if it passed
		return TRUE;
	}
	
}