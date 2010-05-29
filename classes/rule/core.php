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
	
	public static function factory($context, $callback, array $args = NULL)
	{
		return new Rule_Core($context, $callback, $args);
	}
	
	public function __construct($context, $callback, array $args = NULL)
	{
		$this->field = $context;
		$this->callback = $callback;
		$this->name = self::make_name($callback);
		$this->context = $this->make_context($context);
		$this->args = $args ? $args : array();
	}
	
	protected function make_context($context)
	{
		// If :: is in the callback, context is aleady specifically defined
		if (preg_match('/::/', $this->callback))
			return NULL;
			
		// If it's a straight-up function, use that
		if (function_exists($this->callback))
			return NULL;

		// Always check against a possible model first
		if ($model = $context->model() AND method_exists($model, $this->callback))
			return $model;
			
		// Next check against the field
		if (method_exists($context, $this->callback))
			return $context;
			
		// Check for a basic helper function inside Validate
		if (is_callable(array('Validate', $this->callback)))
		{
			$this->callback = 'Validate::'.$this->callback;
			return NULL;
		}
		
		// Or just return the original context
		return $context;
	}
	
	protected static function make_name($callback)
	{
		// Return the function name, not the class namespace
		return preg_replace('/[a-zA-Z0-9_]::/', '', $callback);
	}
			
	public function execute()
	{
		$this->field->pseudo_args($this->args);
		
		if ($this->context !== NULL)
		{
			// If context is set, run the method on the context object
			$method = new ReflectionMethod($this->context, $this->callback);
			if ((bool) $method->invokeArgs($this->context, array_values($this->args)) === FALSE)
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