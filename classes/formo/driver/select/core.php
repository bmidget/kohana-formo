<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Select_Core extends Formo_Driver {

	protected $view = 'select';
	
	public function html()
	{
		$this->render_field->append(Formo_Field::factory('', 'option'));
		
		foreach ($this->render_field->options as $label => $options)
		{
			$options = is_array($options) ? $options : array('value' => $options);
			$checkbox = Formo_Field::factory($label, 'option', $options);
			
			$this->render_field->append($checkbox);
		}
		
		$this->render_field->set('tag', 'select')
			->attr('name', $this->render_field->alias());		
	}

}