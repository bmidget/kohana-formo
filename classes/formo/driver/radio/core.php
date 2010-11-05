<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radio_Core class.
 *
 * @package  Formo
 */
class Formo_Driver_Radio_Core extends Formo_Driver {

	protected $view = 'radio';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'radio')
			->attr('name', $this->field->parent()->name())
			->attr('value', $this->field->val());

		if ($this->field->parent()->val() == $this->field->value)
		{
			$this->field->attr('checked', 'checked');
		}
	}

}
