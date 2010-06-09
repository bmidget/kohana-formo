<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Rule_Core extends Formo_Validator_Rule {
	
	public static function factory($field, $context, $callback, array $args = NULL)
	{
		return new Formo_Rule($field, $context, $callback, $args);
	}
	
	public function __construct($field, $context, $callback, array $args = NULL)
	{
		$this->field = $field;
		$this->context = $context;
		$this->callback = $callback;
		$this->args = (array) $args;
	}
		
	protected static function make_name($callback)
	{
		// Return the function name, not the class namespace
		return preg_replace('/[:]*[a-zA-Z_0-9]+::/', '', $callback);
	}
	
	protected function error()
	{
		// Set the error to the callback name
		$this->error = $this->callback;
		
		// Always return FALSE for an error
		return FALSE;
	}
			
	public function execute()
	{
		// Replace pseudo_args with real values
		$this->field->pseudo_args($this->args);
		
		if ((bool) $this->context === TRUE)
		{
			$method = new ReflectionMethod($this->context, $this->callback);
			$invoke_context = (is_object($this->context)) ? $this->context : NULL;
			
			// If the validate method returns false
			if ((bool) $method->invokeArgs($invoke_context, array_values($this->args)) === FALSE)
				return $this->error();			
		}		
		else
		{
			// Otherwise run the method as a standalone method
			if ((bool) call_user_func_array($this->callback, array_values($this->args)) === FALSE)
				return $this->error();			
		}
		
		// Return TRUE if it passed
		return TRUE;		
	}
	
}