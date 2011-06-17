<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Formo_Kohana config file for dealing with Kohana's official ORM module
 */

return array
(
	'drivers' => array
	(
		'has_many'   => 'checkboxes',
		'belongs_to' => 'select',
		'has_one'    => 'select',
		'default'    => 'input',
	),
);