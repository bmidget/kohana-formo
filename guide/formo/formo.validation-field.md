# Validating a single field

It's simple to do validation against a sigle field. It works just like validating the full form.

### Validate

Just like at the form level by default the field is required to be sent to pass validation.

	$field_validated = $form->username->validate();

Otherwise you can just validate the field against its current value by passing `FALSE`

	$form->username->val($some_value);
	$field_validated = $form->username->validate(FALSE);

### Validating field against another value

Sometimes you will wish to check whether a field would pass validation on a value that hasn't yet been set to the field. Something like this:

	if ($field_passed_validation)
	{
		$form->$field->val($new_val);
	}

Or another common use for this scenario is ajax validation where you need to check if just one field would pass validation.

#### Work with a validation object with rules, labels copied

The way you do this is to retrieve a validation object you can check against. Here's an example. Notice a full validation object is retrieved that you can work with.

	// Retrieve a validation object and populate it with the value $some_username
	$val = $form->username->validation($some_username);
	$passed = $val->check();
	$errors = $val->errors($message_file);