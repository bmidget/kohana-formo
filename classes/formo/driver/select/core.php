<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Select_Core class.
 * 
 * @extends Formo_Driver
 * @package Formo
 */
class Formo_Driver_Select_Core extends Formo_Driver {

	protected $view = 'select';
	
	public function html()
	{
		$this->render_field->append(Formo::field('', 'option'));
		
		foreach ($this->render_field->options as $label => $options)
		{			
			$this->render_field->append(Formo::field($label, 'option', $options));
		}
		
		$this->render_field->set('tag', 'select')
			->attr('name', $this->field->alias());		
	}

}