<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Driver_Div extends Formo_Driver {

	public static function get_tag()
	{
		return 'div';
	}

	public static function get_template( array $array)
	{
		return 'div_template';
	}
}