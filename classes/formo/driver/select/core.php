<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Select_Core class.
 * 
 * @package  Formo
 */
class Formo_Driver_Select_Core extends Formo_Driver {

	protected $view = 'select';
	
	public function html()
	{		
		foreach ($this->field->get('options') as $label => $options)
		{			
			$this->field->append(Formo::field($label, 'option', $options));
		}
		
		$this->decorator
			->set('tag', 'select')
			->attr('name', $this->name());		
	}

}