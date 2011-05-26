<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Radio_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Radio extends Formo_Driver {

	protected $view = 'radio';
	
	public function html()
	{
		$this->decorator
			->set('tag', 'input')
			->attr('type', 'radio')
			->attr('id', $this->name())
			->attr('name', $this->field->parent()->name())
			->attr('value', $this->field->val());

		if ($this->field->parent()->val() == $this->field->val())
		{
			$this->field->attr('checked', 'checked');
		}
	}

}
