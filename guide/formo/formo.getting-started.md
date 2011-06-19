Getting Started
===============

Create a form using the `form` method:

	$form = Formo::form();

Generally you will create a form object and `add` fields to it:

	$form = Formo::form()
		->add('username')
		->add('email');

## Constructs and the $options array

To further understand Formo constructs, see [Construct documentation](formo.constructs).

## The add method

The `add` method takes that parameters `alias, driver, value, options`. If the driver is left out, the default driver is whatever you specify in the config file (default is "text"). Also, for most Formo methods, you can simply pass an array of `options` as the first or second parameter. Thus, the following are the same:

	$form->add('notes', 'textarea', 'My notes');
	$form->add('notes', array('driver' => 'textarea', 'value' => 'My notes'));
	$form->add(array('alias' => 'notes', 'driver' => 'textarea', 'value' => 'My notes'));

This is a general pattern for methods that have a final `options` parameter.

### Aliases and accessing fields

Every field is *added* to the `$form` object is referenced by an alias. The alias for the form is defined above from the `name` parameter. This will also become the name attribute for a field.

You will need to access individual fields. They will be returned by the field's alias or integer order. These three examples will return the same field:

	$form
		->add('subject', 'text')
		->add('notes', 'textarea');

	$form->notes;
	$form->{'notes'};
	$form->{1};

Occasionally you may want to use a number as a field's alias. In order to retrieve the field by its numerical alias, send the string version of the number in the `__get` method:

	$form->add(23, 'text', array(stuff));

	$form->{'23'};

[!!] Form, subform and field aliases always replace spaces with underscores. Thus, the aliases `my field` and `my_field` point to the same field object.

These two are identical:

	$form->add('my field', 'text');
	$form->add('my_field', 'text');

They can be accessed by:

	$form->{'my field'};	
	$form->{'my_field'};

To recap, strings return by alias, integers return by key, `__get()` returns a field by alias and alias spaces are converted to underscores.

### Accessing and setting variables

In order to preserve simple syntax for working with subforms and fields inside formo, built-in variables require the use of function to get and set.

#### Get method

This method returns a variable inside any Container object.

	$form->get('driver');

You can also specify a default value if the variable doesn't exist.

	$form->get('my_variable', array());

#### Set method

To set variables within a Container object, use `set`.

	$form->set('driver', 'group');

You can also pass an array of key => values to set.

	$settings = array('driver' => 'group', 'foo' => 'bar');

	$form->set($settings);

### Formo Fields

Fields are containers for data. Every field you add to your form object is an `Formo_Field` object.

### Subforms

Subforms are added to a form just like a field. They are Formo objects and can contain fields within themselves too. Individually, subforms function exactly like forms.

Fields inside a subform are when rendered as HTML.

For more about subforms, see the [subform section](formo.subforms).

### Find method

This method searches through multi-layered forms and returns a field or subfield by its alias. Or you can specify exactly where the field is by using an array that specifies exactly where the item you're looking for is.

$subform2 = Formo::form()
	->add('bar', 'text');

$subform = Formo::form()
	->add('foo', 'text')
	->add('subform2', $subform2);

$form = Formo::form()
	->add('subform', $subform);

// These will return the same 'bar' field
$form->find(array('subform', 'subform2', 'bar'));
$form->find('bar');

### Rendering

When you render a Formo object, the object will be converted from pure data to a usable object. For instance, if you wish to render your form as html using the defined view files, do:

	$form->render();

This example will convert every field into a HTML DOM object and that object is sent to their defined view files.

See more about rendering in the [rendering section](formo.rendering)
