Subforms
========

## Create a subform on the fly from existing fields

You may want to namespace a part of your form or simply turn a few fields into a subform on the fly. There are many reasons you may want to do this, but one is to allow the "group" or other specific driver for formatting reasons.

Doing this always appends your subform to the bottom of its parent.

To do this, use `create_sub`:

	$form = Formo::factory()
		->add('username')
		->add('email')
		->add('password')
		->add('street')
		->add('city')
		->add('state');
		
	$form->create_sub('address', 'group', array('street', 'city', 'state'), array('before' => 'username'));
	
In `create_sub`, the parameters are the following:

	create_sub(alias, driver, fields, [order])
	
And if the order is a relative order, it's a 'position' => 'relative_to' array, but if it's a hard number, just specify the number. The subform would be in the same place as above if you did this:

	$form->create_sub('address', 'group', array('street', 'city', 'state'), 0);