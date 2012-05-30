<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Input extends Formo_Driver {

	public static function get_tag()
	{
		return 'input';
	}

	public static function get_attr( array $array)
	{
		$field = $array['field'];

		return array
		(
			'type' => 'text',
			'value' => $field->val(),
		);
	}

}