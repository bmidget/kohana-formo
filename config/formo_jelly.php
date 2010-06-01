<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Default drivers associated with Jelly Fields
	'drivers' => array
	(
		'Field_Primary'		=> 'text',
		'Field_String'		=> 'text',
		'Field_Text'		=> 'textarea',
		'Field_Password'	=> 'password',
		'Field_Integer'		=> 'text',
		'Field_BelongsTo'	=> 'select',
		'Field_HasOne'		=> 'select',
	),
	// The names of validation rules to pull from fields
	'validation_keys' => array
	(
		'rules'				=> 'rules',
		'triggers'			=> 'triggers',
		'filters'			=> 'filters',
		'post_filters'		=> 'post_filters',
		
		// Example of no-conflict with built-in Jelly vlaidation
		// 'rules'			=> 'formo_rules',
		// 'triggers'		=> 'formo_triggers',
		// 'rules'			=> 'formo_filters',
		// 'post_filters'	=> 'formo_post_filters',
	),
);