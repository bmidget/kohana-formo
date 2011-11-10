<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// Whether to translate labels
	'translate'              => FALSE,
	// Whether to use Kohana messages
	'use_messages'           => FALSE,
	// File for validate messages
	'message_file'           => 'validate',
	// Default path to view files
	'view_prefix'            => 'formo/html/',
	// For determining path to driver files
	'driver_prefix'          => 'Formo_Driver_',
	// Default form driver
	'form_driver'            => 'form',
	// Default form alias
	'form_alias'             => 'form',
	// Default field driver
	'default_driver'         => 'input',
	// Default render kind
	'kind'                   => 'html',
	// Close single html tags (TRUE = <br/>. FALSE = <br>)
	'close_single_html_tags' => TRUE,
	// Default subform driver
	'default_subform_driver' => 'subform',
	// Namespace fields
	'namespaces'             => FALSE,
	// ORM driver to use
	'orm_driver'             => 'Formo_ORM_Kohana',
	// Config file for the ORM driver
	'orm_config'             => 'formo_kohana',
	// The default ORM primary val because this bug still isn't fixed in ORM
	'orm_primary_val'        => 'name',
	// Auto-generate IDs on form elements
	'auto_id'                => FALSE,
	// If set to true, all Validate helper functions are auto-preceded
	// by :value if it's not explicitly set
	'validate_compatible'    => TRUE,
	// Automatically add these rules to 'input' fields for html5 compatability
	'input_rules' => array
	(
		'email'          => array(array('email')),
		'tel'            => array(array('phone')),
		'url'            => array(array('url')),
		'date'           => array(array('date')),
		'datetime'       => array(array('date')),
		'datetime-local' => array(array('date')),
		'color'          => array(array('color')),
		'week'           => array(array('regex', array(':value', '/^\d{4}-[Ww](?:0[1-9]|[1-4][0-9]|5[0-2])$/'))),
		'time'           => array(array('regex', array(':value', '/^(?:([0-1]?[0-9])|([2][0-3])):(?:[0-5]?[0-9])(?::([0-5]?[0-9]))?$/'))),
		'month'          => array(array('regex', array(':value', '/^\d{4}-(?:0[1-9]|1[0-2])$/'))),
		'range'          => array(
			array('digit'),
			array('Formo_Validator::range', array(':field', ':form')),
		),
		'number'        => array(
			array('digit'),
			array('Formo_Validator::range', array(':field', ':form')),
		),
	),
);
