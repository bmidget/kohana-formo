<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Group_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Group extends Formo_Driver {

	protected $view = 'group';
	public $alias = 'group';

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
