<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Standard address fields
	'address'	=> array
	(
		'street'	=> array
		(
			'_label'		=> 'Street',
		),
		'street2'	=> array
		(
			'_label'		=> 'Street 2',
		),
		'city'		=> array
		(
			'_label'		=> 'City',
		),
		'state'		=> array
		(
			'_label'		=> 'State',
			'_driver'	=> 'select',
			'blank'		=> array(0),
			'_values'	=> array
			(
				'UT'	=> 'Utah',
				'CA'	=> 'California',
				20		=> 'Word up',
			),
			'rules'		=> array
			(
				'not_empty'	=> NULL,
			),
		),
		'zip'		=> array
		(
			'label'		=> 'Zip',
		),
	),
	
	// Standard password fields
	'password'	=> array
	(
		'password'			=> array
		(
			'_driver'	=> 'password',
			'label'		=> 'Password',
		),
		'password_confirm'	=> array
		(
			'_driver'	=> 'password',
			'label'		=> 'Confirm Password',
			'rules'		=> array
			(
				'matches[password]' => array()
			),
		),
	),

);