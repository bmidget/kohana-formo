<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkbox_Core extends Formo_Driver {

	protected $view = 'checkbox';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $field->_alias)
			->attr('value', htmlentities($field->_value));
			
		if ($field->_value)
		{
			$field->_event->run('check', $field);
			$field->attr('checked', 'checked');
		}
	}

}