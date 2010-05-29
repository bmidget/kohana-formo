<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Option_Core extends Formo_Driver {

	protected $view = 'option';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'option')
			->text($field->alias())
			->attr('value', htmlentities($field->val()));
			
		if ($field->parent()->val() == $field->val())
		{
			$field->attr('selected', 'selected');
		}
	}

}