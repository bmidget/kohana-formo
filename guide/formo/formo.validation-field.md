# Validating a single field

You can run validtion against an individual field.
### Validate

[!!] In this context, the individual field never checks if the form was actually sent. It only returns validation based on its value or a given value.

The `validate` method for a field accepts one optional parameter for a value to validate against By default, the field will validate against its current value.

Here's an example using the field's value

	if ($form->username->validate())
	{
	
	}

And here's an en example validating using another value

	if ($form->username->validate($value))
	{
		// If $value is a valid parameter, set the field value to its
		$form->username->val($value);
	}

### Field error

To retrieve the string for the fields' error, use `errors()`. It accepts two optional parameters:

- **file** [default: `NULL`] - The name of the message file. If `NULL`, then the default file from the config is used
- **translate** [default: TRUE] - Whether to translate the message

An example:

	if ($form->username->validate($value) === FALSE)
	{
		// Echo the error message
		echo $form->username->errors();
	}