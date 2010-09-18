<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Validator_Callback_Core extends Formo_Validator_Rule {

	public $type = 'callback';

	public static function factory($callback, array $args = NULL)
	{
		return new Formo_Validator_Callback($callback, $args);
	}

	public function execute()
	{
		parent::execute();
		
		// Return nothing for a callback
		return NULL;
	}

}