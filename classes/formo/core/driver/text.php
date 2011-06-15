<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Text_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Text extends Formo_Driver {

	protected $view = 'text';
		
	public function html()
	{
		$this->view()
			->set_var('tag', 'input')
			->attr('type', 'text')
			->attr('name', $this->name())
			->attr('value', $this->field->val());
	}

}