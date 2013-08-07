<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Group extends Formo_Driver {

	public static function added( array $array)
	{
		$field = $array['field'];

		// Specifically look for attr.enctype="multipart/form-data"
		// and add it to the parent-most form
		if ($field->get('attr.enctype') === 'multipart/form-data')
		{
			// Find the highest-level parent, not just the immediate parent
			$parent = $field->parent();
			while ($parent->parent())
			{
				$parent = $parent->parent();
			}

			$parent->set('attr.enctype', 'multipart/form-data');
			$field->set('attr.enctype', NULL);
		}
	}

	public static function get_template( array $array)
	{
		$field = $array['field'];

		if ($template = $field->get('template'))
		{
			return $template;
		}

		return 'group_template';
	}

	public static function get_val( array $array)
	{
		$field = $array['field'];
		$val = $array['val'];

		$array = array();
		foreach ($field->as_array() as $alias => $field)
		{
			$array[$alias] = $field->val();
		}

		return $array;
	}

	public static function get_validation_values( array $array)
	{
		$field = $array['field'];

		return array($field->alias() => $field->val());
	}

	public static function is_a_parent()
	{
		return TRUE;
	}

}