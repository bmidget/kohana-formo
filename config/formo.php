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
	'default_driver'         => 'input',
	// Default render kind
	'kind'                   => 'html',
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
	'orm_driver'             => 'Formo_ORM_Kohana',
	// Config file for the ORM driver
	'orm_config'             => 'formo_kohana',
	// If set to true, all Validate helper functions are auto-preceded
	// by :value if it's not explicitly set
	'validate_compatible'    => TRUE,
	'input_rules' => array
	(
		'email'         => array(array('email')),
		'tel'           => array(array('phone')),
		'url'           => array(array('url')),
		'date'          => array(array('date')),
		'datetime'      => array(array('date')),
		'datetime-local' => array(array('date')),
		'color'         => array(array('regex', array(':value', '/^#[\da-fA-F]{6}$|([\da-fA-F])\1\1$/'))),
		'week'          => array(array('regex', array(':value', '/^\d{4}-[Ww](?:0[1-9]|[1-4][0-9]|5[0-2])$/'))),
		'time'          => array(array('regex', array(':value', '/^(?:([0-1]?[0-9])|([2][0-3])):(?:[0-5]?[0-9])(?::([0-5]?[0-9]))?$/'))),
		'month'         => array(array('regex', array(':value', '/^\d{4}-(?:0[1-9]|1[0-2])$/'))),
		'range'         => array(
			array('digit'),
			array('Formo_Validator::range', array(':field', ':form')),
		),
		'number'        => array(
			array('digit'),
			array('Formo_Validator::range', array(':field', ':form')),
		),
	),
);
