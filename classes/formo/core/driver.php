<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Core_Driver {

	public static function added( array $array)
	{
		return;
	}

	public static function can_be_empty()
	{
		return false;
	}

	public static function close( array $array)
	{
		$str = $array['str'];

		return $str;
	}

	public static function get_attr( array $array)
	{
		return array();
	}

	public static function get_label( array $array)
	{
		$field = $array['field'];

		$label = $field->get('label');

		return ($label !== Formo::NOTSET)
			? $label
			: $field->alias();
	}

	public static function get_opts( array $array)
	{
		return array();
	}

	public static function get_opts_template( array $array)
	{
		return NULL;
	}

	public static function pre_validate( array $array)
	{
		return;
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

		return 'field_template';
	}

	public static function get_title( array $array)
	{
		return null;
	}

	public static function get_val( array $array)
	{
		return $array['val'];
	}

	public static function get_validation_values( array $array)
	{
		$field = $array['field'];

		return array($field->alias() => $field->val());
	}

	public static function is_a_parent()
	{
		return FALSE;
	}

	public function is_changed()
	{
		
	}

	public static function open( array $array)
	{
		$str = $array['str'];

		return $str;
	}

	public static function load( array $array)
	{
		$val = $array['val'];
		$field = $array['field'];

		$field->val($val);
	}

	public static function name( array $array)
	{
		$field = $array['field'];
		$use_namespaces = $array['use_namespaces'];

		if ($use_namespaces !== TRUE)
		{
			return $field->alias();
		}

		if ($parent = $field->parent())
		{
			$name = $parent->alias().'['.$field->alias().']';
		}
		else
		{
			$name = $field->alias();
		}

		return $name;
	}

	public static function new_val( array $array)
	{
		$new_val = $array['new_val'];

		return $new_val;
	}

}