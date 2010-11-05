<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Default path to view files
	'view_prefix'            => 'formo/html/',
	// For determining path to driver files
	'driver_prefix'          => 'Formo_Driver_',
	// Default form driver
	'form_driver'            => 'form',
	// Default from alias
	'form_alias'             => 'form',
	// Default field driver
	'default_driver'         => 'text',
	// Default render type
	'type'                   => 'html',
	// Close single html tags (TRUE = <br/>. FALSE = <br>)
	'close_single_html_tags' => TRUE,
	// Default subform driver
	'default_subform_driver' => 'subform',
	// File for validate messages
	'message_file'           => 'validate',
	// Namespace fields
	'namespaces'             => TRUE,
	// Whether to translate labels
	'translate'              => TRUE,
	// ORM driver to use
	'orm_driver'             => 'Formo_ORM_Jelly',
	// Config file for the ORM driver
	'orm_config'             => 'formo_jelly',
	// If set to true, all Validate helper functions are auto-preceded
	// by :value if it's not explicitly set
	'validate_compatible'    => TRUE,
);
