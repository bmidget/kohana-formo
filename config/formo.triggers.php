<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	'address2'	=> function($address) {
		if ($address->parent('form')->get('same as billing')->_value != 1)
		{
		
		}
	},

);