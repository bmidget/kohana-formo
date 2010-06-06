<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkboxes_Core extends Formo_Driver {

	protected $view = 'checkboxes';

	public function html()
	{
		foreach ($this->render_field->options as $label => $options)
		{
			$options = is_array($options) ? $options : array('value' => $options);
			$checkbox = Ffield::factory($label, 'checkbox', $options);
			
			$this->render_field->append($checkbox);
		}
	}

}