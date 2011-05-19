# Formo validation errors

This document will help you gain a better understanding of working with validation errors inside Formo.

## Error and errors
Each form, subform and field object can contain an error message.

In addition to this, each form/subform tracks all the errors inside it.

## Setting and retrieving error
The following adds an error message to a field or form. The first parameter is the message and the second optional parameter is whether to translate the message.

	// Attach an error message
	$field->error('This is wrong, wrong wrong');

You retrieve the error with

	$field->error();

## Form/subform errors
You can retrieve an array of a form or subforms errors errors with

	$form->errors([$file], [$translate = TRUE]);

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
		'fields' => array
		(
			'car' => 'car must not be empty',
			'address'	=> array
			(
				'street'	=> 'street must not be empty',
				'zip'		=> 'zip must not be empty',
			)
		)
	)

	//$errors2:
	array
	(
		'fields' => array
		(
			'street'	=> 'street must not be empty',
			'zip'		=> 'zip must not be empty',
		)

	)

## Form/Subform errors

If the form or subform itself has an error, it is included in the list of field errors under the key 'form'. Here's an example of the errors array where the form has errors:

	array
	(
		'form' => 'Unsername or password are incorrect',
	);

## Passing error message parameters

Follow the instruction in the Kohana guide to pass message parameters to error messages. Here's an example:

	public static function my_rule($validation, $field, $value)
	{
		if (condition_not_met())
		{
			$validation->error($field, 'rule_name', array($param1_name, $param2_name));
			return;
		}
		
		return TRUE;
	}
	
[!!] If you explicitly set the error on the validation object in the rule, you need to return `void` instead of `FALSE`.
## Wrap-up

You can attach error messages to any Formo object, but form and subform objects can also fetch an array of all errors inside itself.

Validation fails on a subform based on logic in this order:

1. It has an error message
1. It contains errors within its fields
