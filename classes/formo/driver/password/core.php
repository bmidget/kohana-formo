<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Password_Core extends Formo_Driver {

	protected $view = 'text';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'password')
			->attr('name', $field->alias());
	}

}