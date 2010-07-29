<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Submit_Core class.
 * 
 * @package  Formo
 */
class Formo_Driver_Submit_Core extends Formo_Driver {

	protected $view = 'submit';

	public function html()
	{
		$this->render_field
			->set('tag', 'input')
			->attr('type', 'submit')
			->attr('name', $this->field->alias())
			->attr('value', HTML::entities($this->render_field->value));
	}

}