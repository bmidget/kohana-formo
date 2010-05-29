# Formo validation

Formo provides its own validation system instead of Kohana's packaged Validate library except that it utilizes Validate's helper methods where useful.

Validation is based on filters, rules, triggers and post_filters attached to forms, subforms and individual fields.

## Parameters and pseudo parameters

If no parameter is defined, the only the field's value will be passed to the callback as a parameter.

If any parameters are defined, those are what are passed to the callback.

Because you may need to work with context-specific parameters, the following pseudo parameters are available for any rule, filter or trigger:

Parameter string	|	What is passed
--------------------|-----------------------
':value'			|	The field's value
':field'			|	That field object. Can also be a form/subform
':parent'			|	That field's parent
':form'				|	The topmost parent

Note that if you define any parameter, value is no longer passed by default and has to be specified. For example:

	$form->rule('myfield', 'preg_match', array('/[a-z]+/', ':value'));
	
## Parameter

## Filters

A filter is a callback that pre-processes values for further validation and database saving. A good example of this is stripping a phone number of all non-digit characters.

Filters attached at the form or subform level apply to every one of its fields.

This adds the "trim" filter without any parameters to the form. this will be applied to all fields within the form
	
	$form->filter(NULL, 'trim', NULL);

Here, "trim" will be run only on the username field

	$form->filter('username', 'trim', NULL);
	$form->username->filter(NULL, 'trim', NULL);

## Post Filters
Post filters function exactly like filters but are run on field values only on the rendering object passed into views. Basically, these keep data pretty for the end user.

A good example of a post filter is reformatting a phone number to (xxx) xxx-xxxx format in the view files.

This runs the function "Format::phone($field_value, '(3) 3-4')" on the all fields within $form

	$form->post_filter(NULL, 'Format::phone', array('(3) 3-4'));
	
This runs the same function but only on the field 'phone'

	$form->post_filter('phone', 'Format::phone', array('(3) 3-4'));
	$form->phone->post_filter(NULL, 'Format::phone', array('(3) 304'));

## Rules