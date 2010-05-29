<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Render_Core extends Container
{
	// Allow error to be returned
	public function error()
	{
		return $this->_errors['error'];
	}
	
	// Allow error to be returned
	public function errors()
	{
		return $this->_errors['errors'];
	}
}