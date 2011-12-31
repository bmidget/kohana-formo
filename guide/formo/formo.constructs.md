Methods that involve constructing a new object follow a similar syntax: `param, param2, param3, etc, options_array`.

In these scenarios, think of any params before the final `$options` array as convenience parameters. At any point you can enter an options array that contains all the params.

For instance, consider the `add()` function. Its options are the following: `add(alias, driver, value, options)`.

Here the following are identical:

	$attr = array('height' => '23px');
	$form->add('username', 'input', $model->username, array($attr));
	
	$options = array('value' => $model->username, $attr);
	$form->add('username', 'text', $options);
	
	$options = array('driver' => 'input', 'value' => $model->username, $attr);
	$form->add('username', $options);
	
	$options = array('alias' => 'username', 'value' => $model->username, 'driver' => 'input', $attr);
	$form->add($options);
	
This is the case with all Formo constructs.