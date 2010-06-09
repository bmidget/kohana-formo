<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Validator_Rule_Core {

	// The field the rule is attached to
	public $field;
	// The context the rule is to be called against
	public $context;
	// The callback for the rule
	public $callback;
	// Args for the callback
	public $args = array();
	
	// Any errors associated with the rule
	public $error = FALSE;
	
}