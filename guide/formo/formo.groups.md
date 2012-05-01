Groups
======

Select boxes, radio groups and checkbox groups are all examples of fields that rely on a set of *options* to choose from.

Since it's often cumbersome to have to specify the options parameter in the options array when adding or creating a field, Formo provides the convenience method `add_group()`.

## add_group usage

The parameters:

* alias
* driver
* options
* value
* settings (optional)

Note that the general Formo construct rule doesn't apply to `add_group`. each field has to be explicitly defined with the exception of *settings*, which is optional.

## Examples

For the following examples, we will be using this array of options:

	$options = array
	(
		'run' => 'Running',
		'swim' => 'Swimming',
		'bike' => 'Biking',
		'hike' => 'Hiking',
	);
	
You can either use `alias => value` pairs or individually specify all of the parameters for greater control.

Without `add_group()`, you would have to do this:

	$form->add('hobbies', 'select', 2, array('options' => $options));
	
But `add_group()` makes the syntax more obvious:

	$form->add_group('hobbies', 'select', $options, 2);