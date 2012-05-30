<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Select extends Formo_Driver {

	public static function get_opts( array $array)
	{
		$field = $array['field'];

		$opts_array = array();
		foreach ($field->get('opts', array()) as $key => $value)
		{
			$opts_array[] = '<option value="'.$key.'">'.$value.'</option>';
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