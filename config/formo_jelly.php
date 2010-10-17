<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Default drivers associated with Jelly Fields
	'drivers' => array
	(
		'Jelly_Field_Primary'				=> 'text',
		'Jelly_Field_String'				=> 'text',
		'Jelly_Field_Text'				=> 'textarea',
		'Jelly_Field_Password'			=> 'password',
		'Jelly_Field_Integer'				=> 'text',
		'Jelly_Field_Float'				=> 'text',
		'Jelly_Field_Boolean'				=> 'bool',
		'Jelly_Field_BelongsTo'			=> 'select',
		'Jelly_Field_HasOne'				=> 'select',
		'Jelly_Field_HasMany'				=> 'checkboxes',
		'Jelly_Field_ManyToMany'			=> 'checkboxes',
	),
	// Rules to attach according to Jelly Field parameter values
	'auto_rules' => array
	(
		/* Example auto_rule
		'unique'	=> array
		(
			TRUE,
			array(':model::unique' => array(':alias');
		),
		*/
	),
	// The names of validation rules to pull from fields
	'validation_keys' => array
	(
		'rules'				=> 'rules',
		'triggers'			=> 'triggers',
		'filters'			=> 'filters',
		'post_filters'		=> 'post_filters',
		
		/* Example of no-conflict with built-in Jelly vlaidation
		'rules'			=> 'formo_rules',
		'triggers'		=> 'formo_triggers',
		'rules'			=> 'formo_filters',
		'post_filters'	=> 'formo_post_filters',
		*/
	),
);