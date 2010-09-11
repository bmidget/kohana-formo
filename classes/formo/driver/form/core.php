<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Form_Core class.
 *
 * @package  Formo
 */
class Formo_Driver_Form_Core extends Formo_Driver {

	protected $view = 'form';
	public $alias = 'form';

	// Setup the html object
	public function html()
	{
		$this->decorator
			->set('tag', 'form');

		// If it's not already defined, the form's type is 'post'
		( ! $this->decorator->attr('method') AND $this->decorator->attr('method', 'post'));

		// If it's not already defined, define the field's action
		( ! $this->decorator->attr('action') AND $this->decorator->attr('action', ''));
	}

}
