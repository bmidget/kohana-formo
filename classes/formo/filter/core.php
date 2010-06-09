<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Filter_Core extends Formo_Validator_Rule {
	
	public static function factory($context, $callback, array $args = NULL)
	{		
		return new Formo_Filter($context, $callback, $args);
	}

	public function __construct($context, $callback, array $args = NULL)
	{
		$this->context = $context;
		$this->callback = $callback;
		$this->args = (array) $args;
	}
	
	public function execute()
	{
		$this->context->pseudo_args($this->args);
		
		// Set the context's value to whatever the filter returns
		$this->context->val(call_user_func_array($this->callback, $this->args));
		
		return TRUE;
	}

}