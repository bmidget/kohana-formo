<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @extends Formo_Driver
 * @package Formo
 */
class Formo_Driver_Text_Core extends Formo_Driver {

	protected $view = 'text';
	
	public function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'text')
			->attr('name', $this->field->alias())
			->attr('value', HTML::entities($this->render_field->value));
	}

}