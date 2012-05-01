# Input examples

## Add a text input

Text inputs are the default driver type. The 'text' is not required.

	$form->add('username');

## HTML 5 Inputs

This is determined by the `type` attribute, just like in the html.

	$form->add('website', array('type' => 'url'));