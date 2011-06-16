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
		$this->view()
			->set_var('tag', 'input')
			->attr('type', 'radio')
			->attr('name', $this->field->parent()->name())
			->attr('value', $this->field->val());

		if ($this->field->parent()->val() == $this->field->val())
		{
			$this->field->view()->attr('checked', 'checked');
		}
	}

}
