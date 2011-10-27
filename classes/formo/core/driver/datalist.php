<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Datalist extends Formo_Driver {

	protected $_view_file = 'datalist';

	public function html()
	{
		$this->_view
			->set_var('tag', 'datalist')
			->attr('id', $this->_field->get('id'));
	}

}
