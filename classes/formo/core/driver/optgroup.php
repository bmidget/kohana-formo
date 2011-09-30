<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Optgroup extends Formo_Driver {

	protected $_view_file = 'optgroup';
	
	public function val($value = NULL)
	{
		if (func_num_args() === 0)
			return $this->_field->parent()->val();
		
		return $this->_field->parent()->val($value);
	}
	
	public function html()
	{
		foreach ($this->_field->get('options') as $label => $options)
		{
			$this->_field->append(Formo::field($label, 'option', $options));
		}

		$this->_view
			->set_var('tag', 'optgroup')
			->attr('label', $this->_field->alias());
	}

}