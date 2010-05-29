<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Text_Core extends Formo_Driver {

	protected $view = 'text';
	
	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'text')
			->attr('name', $field->alias())
			->attr('value', htmlentities($field->val()));
	}

}