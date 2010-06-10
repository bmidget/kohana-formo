<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Default path to view files
	'view_prefix'				=> 'formo/',
	// Default form driver
	'form_driver'				=> 'form',
	// Default from alias
	'form_alias'				=> 'form',
	// Default field driver
	'default_driver'			=> 'text',
	// Default render type
	'render_type'				=> 'html',
	// Default subform driver
	'default_subform_driver'	=> 'subform',
	// File for validate messages
	'message_file'				=> 'validate',
	// Namespace fields
	'namespaces'				=> TRUE,
	// Whether to translate labels
	'translate'					=> TRUE,
	// ORM driver to use
	'orm_driver'				=> 'Formo_ORM_Jelly',
	// Config file for the ORM driver
	'orm_config'				=> 'formo_jelly',
	// If set to true, all Validate helper functions are auto-preceded
	// by :value if it's not explicitly set
	'validate_compatible'		=> TRUE,
		
	// Classes for objects that are passed to render view files
	'render_classes'			=> array
	(
		'html'					=> 'Formo_Render_HTML',
		'json'					=> 'Formo_Render_Json',
		'xml'					=> 'Formo_Render_XML',
	),
);