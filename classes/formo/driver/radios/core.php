<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Radios_Core extends Formo_Driver {

	protected $view = 'radios';
	
	public function pre_render_html($field)
	{
		foreach ($field->options as $label => $options)
		{
			$options = is_array($options) ? $options : array('value' => $options);
			$checkbox = Ffield::factory($label, 'radio', $options);
						
			$field->append($checkbox);
		}
	}

}