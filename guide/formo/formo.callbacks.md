# Callbacks

Callbacks can be attached to form or field objects, and can be run under two conditions after validation:

1. Callbacks run after form or field passes validation ("pass")
2. Callbacks run after form or field fails validation ("fail")

The callbacks are run immediately after the object they are attached to pass or fail validation. Thus, if a "pass" callback is attached to a field, it will run the moment after that field passes validation.

If the same callback is attached to the form object, it will be run after the parent form passes validation.

## Form callbacks

Callbacks for any field inside the form can be attached to a form object.

Here's an example of attaching a `some_model::post_process` callback to a field, and `another_model::post_process` to a form both run after the form passes validation and `my_model::failed_validation` callback when the form fails validation.

	$form->callbacks(array(
		'pass' => array(
			':self' => array(
				array('another_model::post_process', array(':field'))
			),
			'email' => array(
				array('some_model::post_process', array(':value', ':last_val'))
			)
		),
		'fail' => array(
			'username' => array(
				array('my_model::failed_validation(':value', ':last_val'))
			)
		),
	));

## Adding callbacks

### Add callbacks as a group

You can add a group of callbacks to an object using the `callbacks` method.

[!!] When attaching a callback to the form object itself, refer to it as `:self`

	$form->callbacks(array(
		'pass' => array(
			':self' => array(
				array('another_model::post_process', array(':field')),
				array('another_model2::post_process', array(':field', ':value'))
			),
			'email' => array(
				array('some_model::post_process', array(':value', ':last_val'))
			)
		),
		'fail' => array(
			':self' => array(
				array('some_method', array(':field', ':value'))
			),
			'username' => array(
				array('my_model::failed_validation(':value', ':last_val'))
			)
		),
	));

When attaching callbacks to a field, you do not specify a field name. All callbacks attached to the field apply to the field object.

	$form->email->callbacks(array(
		'pass' => array(
			array('some_method', array(':value')),
			array('foo::bar', array(':field'))
		),
		'fail' => array(
			array('foobar', array(':value'))
		),
	));

### Add callbacks individually

Use `callback` to attach individual callbacks to a form or field.
	
For a form, the syntax looks like this:

`callback(type, alias, method, [values])`

- type ("pass" or "fail")
- alias (":self" for form object)
- method
- values (default `NULL`)

~~~
$form->callback('pass', 'email', 'foo::bar', array(':value'));
~~~

For a field, the syntax looks like this:

`callback(type, method, [values])`

- type ("pass" or "fail")
- method
- values (default `NULL`)

~~~
$form->$field->callback('fail', 'foo::bar', array(':field', ':value'));
~~~

## Defining callbacks at form or field creation

Create an array of callbacks that look like the arrays attached using the `callback` methods.

~~~
$form = Formo::form(array(
	'callbacks' => array(
		'pass' => array(
			':self' => array(
				array('foo::bar', array(':field'))
			)
			'some_field' => array(
				array('foobar', array(':field', ':value', ':last_val'))
			),
		),
		'fail' => $fail_callbacks
	)
));
~~~

And a field
~~~
$form->add('username', array(
	'callbacks' => array(
		'pass' => array(
			array('foo::bar'),
			array('foobar', array(':value', 'last_val'))
		),
		'fail' => $fail_callbacks
	)
));
~~~

## Callback special parameters

You can pass special parameters to the callback

- `:field` - the field or form object
- `:value` - the field or form value
- `:last_val` - the field's previous value