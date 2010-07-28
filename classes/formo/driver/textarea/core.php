<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Textarea_Core class.
 * 
 * @extends Formo_Driver
 * @package Formo
 */
class Formo_Driver_Textarea_Core extends Formo_Driver {

	protected $view = 'textarea';
	
	public function html()
	{
		$this->render_field
			->set('tag', 'textarea')
			->set('text', HTML::entities($this->render_field->value))
			->attr('name', $this->field->alias());
	}

}