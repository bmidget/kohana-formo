<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Core_Driver {

	public static function get_attr( array $array)
	{
		return array();
	}

	public static function get_label( array $array)
	{
		$field = $array['field'];

		if ($label = $field->get('label'))
		{
			return $label;
		}

		return $field->alias();
	}

	public static function get_tag()
	{
		
	}

	public static function get_template( array $array)
	{
		$field = $array['field'];

		if ($template = $field->get('template'))
		{
			return $template;
		}

		return 'formo/field_template';
	}

	public static function get_title( array $array)
	{
		return null;
	}

	public static function get_opts( array $array)
	{
		return array();
	}

	public static function get_opts_template( array $array)
	{
		return NULL;
	}

	public static function get_val( array $array)
	{
		return $array['val'];
	}

	public function is_changed()
	{
		
	}

	public static function load( array $array)
	{
		$val = $array['val'];
		$field = $array['field'];

		$field->val($val);
	}

	public static function new_val( array $array)
	{
		$new_val = $array['new_val'];

		return $new_val;
	}

}