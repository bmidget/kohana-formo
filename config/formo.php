<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Path to view files
	'view_prefix'				=> 'formo/',
	// Default form driver
	'form_driver'				=> 'form',
	// Default field driver
	'default_driver'			=> 'text',
	// Default subform driver
	'default_subform_driver'	=> 'subform',
	// File for validate messages
	'validate_messages_file'	=> 'validate',
	// Whether to translate labels
	'translate_labels'			=> TRUE,
	// Whether to translate messages
	'translate_messages'		=> TRUE,
	// ORM driver to use
	'ORM'						=> 'Jelly',
	
	// Groups are for adding groups of fields
	'groups'					=> array
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
	),
);