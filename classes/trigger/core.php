<?php defined('SYSPATH') or die('No direct script access.');

class Trigger_Core {

	public $context;

	protected $callback;
	public $args = array();
	
	protected $action_fields = array();
	protected $action_callbacks = array();
	protected $action_args = array();
	
	public static function factory($callback, array $args = NULL, $check_against = TRUE)
	{
		return new Trigger($callback, $args, $check_against = TRUE);
	}
	
	public function __construct($callback, array $args = NULL, $check_against = TRUE)
	{
		$this->callback = $callback;
		$this->args = $args !== NULL ? $args : array();
		$this->check_against = $check_against === FALSE;
	}
	
	// Add the trigger's context
	public function context($context)
	{
		$this->context = $context;
		
		return $this;
	}
	
	// Add a single action to the trigger
	public function action($action_field, $action_callback, array $action_args = NULL)
	{
		$this->action_fields[] = $action_field;
		$this->action_callbacks[] = $action_callback;
		$this->action_args[] = $action_args !== NULL ? $action_args : array();
		
		return $this;
	}
	
	public function execute()
	{
		$this->context->pseudo_args($this->args);
		// Create a rule
		$rule = Rule::factory($this->context, $this->callback, $this->args);
		
		// Execute the rule, and if no errors are returned, perform the actions
		if ($rule->execute() === $this->check_against)
		{
			// Use the parent for executing actions
			$parent = $this->context->parent(Container::PARENT);
						
			foreach($this->action_fields as $key => $field)
			{
				// Fix the args
				$this->context->pseudo_args($this->action_args[$key]);
				
				// Perform each action
				$callback = array($parent->find($field), $this->action_callbacks[$key]);
				call_user_func_array($callback, $this->action_args[$key]);
			}
		}
	}

}