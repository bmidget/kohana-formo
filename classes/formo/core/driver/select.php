<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Select_Core class.
 * 
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Select extends Formo_Driver {

	protected $view = 'select';
	
	public function html()
	{		
		foreach ($this->field->get('options') as $label => $options)
		{			
			$this->field->append(Formo::field($label, 'option', $options));
		}
		
		$this->decorator
			->set('tag', 'select')
			->attr('id', $this->name())
			->attr('name', $this->name());		
	}

}