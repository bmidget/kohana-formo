<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Form extends Formo_Driver {

	public static function get_attr( array $array)
	{
		$field = $array['field'];

		return array
		(
			'method' => ($method = $field->attr('method')) ? $method : 'post',
			'action' => ($action = $field->attr('action')) ? $action : '',
		);
	}

	public static function get_tag()
	{
		return 'form';
	}

	public static function get_template( array $array)
	{
		$field = $array['field'];

		if ($template = $field->get('template'))
		{
			return $template;
		}

		return 'formo/form_template';
	}

	public static function get_val( array $array)
	{
		$field = $array['field'];
		$val = $array['val'];

		$array = array();
		foreach ($field->as_array('val') as $alias => $val)
		{
			$array += array($alias => $val);
		}

		return $array;
	}
}