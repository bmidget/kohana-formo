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
		$this->decorator
			->set('tag', 'form');

		// If it's not already defined, the form's type is 'post'
		( ! $this->decorator->attr('method') AND $this->decorator->attr('method', 'post'));

		// If it's not already defined, define the field's action
		( ! $this->decorator->attr('action') AND $this->decorator->attr('action', ''));
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
