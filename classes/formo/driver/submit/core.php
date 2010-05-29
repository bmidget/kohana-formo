<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Submit_Core extends Formo_Driver {

	protected $view = 'submit';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'submit')
			->attr('name', $field->_alias)
			->attr('value', htmlentities($field->val()));
	}

}