<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Input extends Formo_Driver {

	public static function get_tag()
	{
		return 'input';
	}

	public static function get_attr( array $array)
	{
		$field = $array['field'];

		$type = ($_type = $field->attr('type'))
			? $_type
			: 'text';

		$val = ($type == 'password')
			? NULL
			: $field->val();

		return array
		(
			'type' => $type,
			'value' => $val,
			'name' => $field->alias(),
		);
	}

	public static function get_label( array $array)
	{
		$field = $array['field'];

		if ($field->attr('type') == 'submit')
		{
			return NULL;
		}
		else
		{
			return parent::get_label($array);
		}
	}

}