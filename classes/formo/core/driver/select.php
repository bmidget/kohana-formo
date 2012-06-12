<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Select extends Formo_Driver {

	public static function get_attr( array $array)
	{
		$field = $array['field'];

		return array
		(
			'name' => $field->alias(),
		);
	}

	public static function get_opts( array $array)
	{
		$field = $array['field'];

		$opts_array = array();

		if ($field->get('blank') === TRUE)
		{
			$opts_array[] = '<option></option>';
		}

		foreach ($field->get('opts', array()) as $key => $value)
		{
			$selected = ($value == $field->val())
				? ' selected="selected"'
				: NULL;

			$opts_array[] = '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}

		return $opts_array;
	}

	public static function get_opts_template( array $array)
	{
		return 'formo/opts/select_template';
	}

	public static function get_tag()
	{
		return 'select';
	}

}