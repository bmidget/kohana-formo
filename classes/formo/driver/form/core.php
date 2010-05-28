<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Form_Core extends Formo_Driver {

	protected $view = 'form';

	public function pre_render_html($form)
	{
		$hidden_field = Ffield::factory('_formo', 'hidden', array('value' => $form->alias()));

		// A a special hidden fielm just to the HTML form
		$form->prepend($hidden_field);

		$form->set('tag', 'form')
			->attr('method', $this->get('method', 'post'))
			->attr('action', $this->get('action', ''));
	}

}