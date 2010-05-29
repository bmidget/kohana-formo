<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Radio_Core extends Formo_Driver {

	protected $view = 'radio';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'radio')
			->attr('name', $field->parent()->alias())
			->attr('value', htmlentities($field->val()));
			
		if ($field->parent()->val() == $field->val())
		{
			$field->attr('checked', 'checked');
		}
	}

}