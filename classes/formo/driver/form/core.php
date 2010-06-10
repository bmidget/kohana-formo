<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Form_Core extends Formo_Driver {

	protected $view = 'form';
	protected $alias = 'form';
	
	// Setup the html object
	public function html()
	{
		$this->render_field->set('tag', 'form')
			->attr('method', $this->field->get('method', 'post'))
			->attr('action', $this->field->get('action', ''));
	}

}