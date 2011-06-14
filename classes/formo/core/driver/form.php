<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Form_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Form extends Formo_Driver {

	protected $view = 'form';
	public $alias = 'form';

	// Setup the html object
	public function html()
	{
		$this->view()
			->set_var('tag', 'form');

		// If it's not already defined, the form's type is 'post'
		( ! $this->view()->attr('method') AND $this->view()->attr('method', 'post'));

		// If it's not already defined, define the field's action
		( ! $this->view()->attr('action') AND $this->view()->attr('action', ''));
	}
	
	public function val($value = NULL)
	{
		$values = array();
		foreach ($this->field->get('fields') as $field)
		{
			$values[$field->alias()] = $field->val();
		}
		
		return $values;
	}

}
