<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Textarea_Core extends Formo_Driver {

	protected $view = 'textarea';
	
	public function pre_render_html($field)
	{
		$field
			->set('tag', 'textarea')
			->set('text', htmlentities($field->val()))
			->attr('name', $field->alias());
	}

}