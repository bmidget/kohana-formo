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
);