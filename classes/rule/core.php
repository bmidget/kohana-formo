<?php defined('SYSPATH') or die('No direct script access.');

class Rule_Core {

	// The field the rule is attached to
	public $field;
	// The object to run the callback on
	public $context;
	// The callback function
	public $callback;
	// The name of the rule, used as the error name
	public $name;

	// Additional args
	public $args = array();
	// The error resulted from this rule
	public $error = FALSE;
	// The messages file
	public $file = 'validate';
	
	public static function factory($field, $context, $callback, array $args = NULL)
	{
		return new Rule_Core($field, $context, $callback, $args);
	}
	
	public function __construct($field, $context, $callback, array $args = NULL)
	{
		$this->field = $field;
		$this->context = $context;
		$this->callback = $callback;
		$this->name = self::make_name($callback);
		$this->args = $args ? $args : array();		
	}
		
	protected static function make_name($callback)
	{
		// Return the function name, not the class namespace
		return preg_replace('/[:]*[a-zA-Z_0-9]+::/', '', $callback);
	}
			
	public function execute()
	{
		$this->field->pseudo_args($this->args);
		
		if ($this->context !== NULL AND is_object($this->context))
		{
			// If context is set, run the method on the context object
			$method = new ReflectionMethod($this->context, $this->callback);
			if ((bool) $method->invokeArgs($this->context, array_values($this->args)) === FALSE)
				return $this->error = $this->name;
		}
		elseif ($this->context !== NULL)
		{
			if ((bool) call_user_func_array(array($this->context, $this->callback), array_values($this->args)) === FALSE)
				return $this->error = $this->name;
		}
		else
		{
			// Otherwise run the method as a standalone method
			if ((bool) call_user_func_array($this->callback, array_values($this->args)) === FALSE)
				return $this->error = $this->name;
		}

		// Return the error as a default catch-all
		return $this->error;
	}
	
}