# Working with files

File fields function like any other Formo fields except its value is always populated from the $_FILES array.

You will also use Kohana's Upload rules instead of Validate rules.

[!!] When working with HTML forms, the field's parent is automatically added the *enctype="multipart/form-data"*, but only for field's immediate parent

[!!] $_FILES cannot be namespaced, thus neither are file fields

### Example

This is an example of a adding a required file input field that must be an image less than 1 mb

	$form = Formo::form()
		->add('logo', 'file')
		->rules('logo', array(
			'Upload::not_empty' => NULL,
			'Upload::type'      => array(':value', 'PNG, PNG or GIF' => array('jpg', 'png', 'gif')),
			'Upload::size'      => array(':value', '1M'),
		));
		
	if ($form->load()->validate())
	{
		// This is the raw data from the $_FILES[$filename] array
		$file_data = $form->logo->val();

		// Do something with $file_data
	}