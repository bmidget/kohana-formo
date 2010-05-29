<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Driver_Checkbox_Core extends Formo_Driver {

	protected $view = 'checkbox';

	public function pre_render_html($field)
	{
		$field
			->set('tag', 'input')
			->attr('type', 'checkbox')
			->attr('name', $this->parent()->alias().'[]')
			->attr('value', htmlentities($field->val()));
		
		$parent_value = $field->parent()->val();
				
		if (is_array($parent_value) AND in_array($field->val(), $parent_value))
		{
			$field->attr('checked', 'checked');
		}
	}

}