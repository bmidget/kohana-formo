<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Validator_Item_Core {

	// The field type
	public $type;
	// The context the rule is to be called against
	public $context;
	// The callback for the rule
	public $callback;
	// Args for the callback
	public $args = array();
	
	// Any errors associated with the rule
	public $error = FALSE;
	
	public function field($field)
	{
		$this->field = $field;
		
		return $this;
	}
	
}