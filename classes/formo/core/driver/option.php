<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Driver_Option_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
class Formo_Core_Driver_Option extends Formo_Driver {

	protected $view = 'option';

	public function html()
	{
		$this->decorator
			->set('tag', 'option')
			->text($this->field->alias())
			->attr('value', $this->field->val());

		if ($this->field->parent()->val() == $this->field->val())
		{
			$this->field->attr('selected', 'selected');
		}
	}
	
	public function format_alias($alias)
	{
		return $alias;
	}

}
