Ordering fields
===============

You can specify a place for a new field to be inserted or move fields around after the fact.

### New field order

The option that describes a special place for your new field to be appended at is "order". You can either use a number for a specific place or place it relative to another field.

#### Specific order

The following example adds 'email' at a specific place:

	$form = Formo::form()
		->add('username')
		->add('password')
		->add('email', array('order' => 1));
		
Ordering begins at 0, therefore the above form adds the fields in the following order:

	'username', 'email', 'password'
	
#### Relative order

You can also choose a place relative to another field. The options are "before" and "after".

	$form = Formo::form()
		->add('username')
		->add('password')
		->add('email', array('order' => array('before', 'password')))
		->add('firstname', array('order' => array('after', 'email')));
		
This example places the fields in the following order:

	'username', 'email', 'firstname', 'password'
	
	
### Order method

Move fields around after creation by using the `order` method.

	$form->order(array('email' => 1));
	
Or
	
	$form->order(array(
		'email' => 2,
		'username' => array('before', 'first_name'),
		'password' => array('after', 'last_name'),
	));

### Limitations

You cannot reserve seats when ordering fields. That is, if a field was specified as being "before email" and email is added later, the original field will not move.

Order is always based on the fields already added to the form or subform.

Formo will not magically sift through the depths of your subforms when specifying an order. You must either specify the order based on the field's context.