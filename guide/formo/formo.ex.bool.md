# Bool examples

## Add a boolean checkbox

	$form->add('agree', 'bool', $checked, array('label' => 'I agree with the terms and conditions'));

## Make a boolean checkbox pre-checked

	$form->add('agree', 'bool', 1, array('label' => 'I agree with the terms and conditions'));