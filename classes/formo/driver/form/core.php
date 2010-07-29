<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Form_Core class.
 * 
 * @package  Formo
 */
class Formo_Driver_Form_Core extends Formo_Driver {

	protected $view = 'form';
	protected $alias = 'form';
	
	// Setup the html object
	public function html()
	{
		$this->render_field->set('tag', 'form')
			->attr('method', $this->field->get('method', 'post'));
		
		// If it's not already defined, define the field's action	
		(empty($this->render_field->attr['action']) AND $this->render_field->attr['action'] = '');
	}

}