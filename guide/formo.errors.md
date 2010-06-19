# Formo validation errors

This document will help you gain a better understanding of working with validation errors inside Formo.

## Error and errors
Each form, subform and field object can contain an error message.

In addition to this, each form/subform tracks all the errors inside it.

## Setting and retrieving error
The following adds an error message to a field or form. The first parameter is the message and the second optional parameter is whether to translate the message.

	// Translate the message
	$field->error('error_msg', TRUE);
	// Just attach the message
	$field->error('This is wrong, wrong wrong');
	
You retrieve the error with

	$field->error();

## Form/subform errors
You can retrieve an array of a form or subforms errors errors with

	$form->errors();
	
The error messages are hierarchal. Let's explore a form with a subform.

In this example, assume every field has the rule 'not_empty'.

	$address = Formo::form()
		->add('street')
		->add('zip');
		
	$form = Formo::form()
		->add('car', 'select', $hobbies)
		->add('address', $address);
		
When the form submits empty, the following errors exist:

	$errors = $form->errors();
	$errors2 = $address->errors();

Returns
	
	
	//$errors:
	array
	(
		'car' => 'car must not be empty',
		'address'	=> array
		(
			'street'	=> 'street must not be empty',
			'zip'		=> 'zip must not be empty',
		)
	)
	
	//$errors2:
	array
	(
		'street'	=> 'street must not be empty',
		'zip'		=> 'zip must not be empty',
	)
	
## Wrap-up

You can attach error messages to any Formo object, but form and subform objects can also fetch an array of all errors inside itself.

Validation fails on a subform based on logic in this order:

1. It has an error message
1. It contains errors within its fields