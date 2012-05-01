Subforms
========

## Subform description

A subform is a form within a form. Formo supports deep form objects and allows as many forms to exist within other forms as you deem necessary.

## Subform basics

Since a subform is just a form within a form, it's creation is just like a form. Then it's added to a parent form.

	$address = Formo::form()
		->add('street')
		->add('city')
		->add('zip');
		
	$user_form = Formo::form()
		->add('first name')
		->add('last name')
		->add('address', 'group', $address);
		
In the example above, the *address* form is added to *$user_form* after *last_name*. It is given the alias *address* and is using the driver *group* to handle it.

If you wanted to access *street*, to set its value for instance, you could do this:

	$user_form->address->street->val();
	
Additionally, since PHP passes objects by reference, you could access the same value from the orinal subform object too:

	$address->street->val();
	
Note that you do not have to define a driver for your form object. It will use the default *form* driver is one isn't specified. This example adds the *address* subform to *user_form* but the driver is the default form driver:

	$user_form->add('address', $address);

## Create a subform on the fly from existing fields

You may want to namespace a part of your form or simply turn a few fields into a subform on the fly. There are many reasons you may want to do this, but one is to allow the "group" or other specific driver for formatting reasons.

Doing this always appends your subform to the bottom of its parent.

To do this, use `create_sub`:

	$form = Formo::form()
		->add('username')
		->add('email')
		->add('password')
		->add('street')
		->add('city')
		->add('state');
		
	$form->create_sub('address', 'group', array('street', 'city', 'state'), array('before' => 'username'));

[!!] `create_sub()` creates the subform within its parent and returns that subform object.
	
In `create_sub`, the parameters are the following:

	create_sub(alias, driver, fields, [order])
	
And if the order is a relative order, it's a 'position' => 'relative_to' array, but if it's a hard number, just specify the number. The subform would be in the same place as above if you did this:

	$form->create_sub('address', 'group', array('street', 'city', 'state'), 0);