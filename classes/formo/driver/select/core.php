<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Select_Core extends Formo_Driver {

	protected $view = 'select';
	
	public function pre_render_html($field)
	{
		$field->append(Ffield::factory('', 'option'));
		
		foreach ($field->options as $label => $options)
		{
			$options = is_array($options) ? $options : array('value' => $options);
			$checkbox = Ffield::factory($label, 'option', $options);
			
			$field->append($checkbox);
		}
		
		$field->set('tag', 'select')
			->attr('name', $field->alias());		
	}

}