<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Hidden_Core extends Formo_Driver {

	protected $view = 'hidden';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'hidden')
			->attr('name', $field->alias())
			->attr('value', htmlentities($field->value));
	}

}