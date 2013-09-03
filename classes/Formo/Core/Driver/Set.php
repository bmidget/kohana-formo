<?php defined('SYSPATH') or die('No direct script access.');

// Set Driver, used specifically for Mysql SET type
class Formo_Core_Driver_Set extends Formo_Driver {

	public static function can_be_empty()
	{
		return TRUE;
	}

	public static function get_label( array $array)
	{
		return;
	}

	public static function get_opts( array $array)
	{
		$field = $array['field'];

		$opts_array = array();
		foreach ($field->get('opts', array()) as $key => $value)
		{
			$opts_array[] = '<input type="checkbox" name="'.$field->name().'[]" value="'.$key.'" />';
		}

		return $opts_array;
	}

	public static function get_opts_template( array $array)
	{
		return 'opts/set_template';
	}

	public static function get_title( array $array)
	{
		$field = $array['field'];

		$label = $field->get('label');

		return ($label !== Formo::NOTSET)
			? $label
			: $field->alias();
	}

	public static function get_val( array $array)
	{
		$val = $array['val'];
		if (is_string($val))
		{
			$val = explode(',', $val);
		}

		return $val
			? implode(',', $val)
			: '';
	}

}