# Datalist example

## Create a datalist

	$form->add('cars', 'datalist', array(
		'id' => 'cars',
		'options' => array('BMW', 'Mercedes', 'Honda', 'Ford'),
	));

[!!] An `id` and `options` are required for the datalist to work.