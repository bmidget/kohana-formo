# Using custom message files

By default, the file message file `validation.php` is used. But you can specify specific message files to use for any form, subform or even individual field.

	$form->set('message_file', 'somefile');
	$form->{$some_field}->set('message_file', 'anotherfile');

	// And as alays, at creation in the options array
	$form = Formo::form(array('message_file' => $filename));