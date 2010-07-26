# loading $_POST values

New field values are added with the `load` method. The method accepts an array of key => value pairs that correspond with field aliases. If an array is not passed in the method, Formo loads uses the $_POST array.

This method also sets whether the form is considered "sent". It is considered sent if any of the fields were present in the array from `load()`.

	// This loads $_POST
	$form->load();
	
	// This also loads the $_POST values
	$form->load($_POST);
	
	// This loads the $_GET values
	$form->load($_GET);
	
	// This loads another array of values
	$form->load($vals);
	
# Sent method

Though this is used internally, it is a public method that you may find the need for. Pass an input array as the parameter and it returns a *boolean* value whether the form was sent or not.

If a field from the input array exists in the form/subform, `sent()` returns `TRUE`.
	
# Validate method

The `validate` method checks each field against any validation rules that are assigned to it, attaches errors to any fields or subforms that didn't pass, and returns a **boolean** representing whether the form passed (`TRUE`) or failed (`FALSE`) validation.

If the form wasn't sent (that is, there are weren't any field values in the `load()` array), `validate()` will also return `FALSE`. If you need to run validation on the form even if it wasn't sent by passing `TRUE` as a parameter.

Since you will generally check validation rules after adding an input array, `validate()` will likely be coupled with `load()`.

You can also run `validate()` on a single field.

Here are some examples:

	// Attach error messages and check if form passes validation
	if ($form->load()->validate())
	
	// Attach error messages and check if email field passed validation
	if ($form->load()->email->validate())
	
	// Attach error messages and check if form passes validation even if it wasn't sent
	if ($form->validate(TRUE))

# Formo validation

Formo provides its own validation system instead of Kohana's packaged Validate library except that it utilizes Validate's helper methods where useful.

Validation is based on filters, rules, triggers and post_filters attached to forms, subforms and individual fields.

## Parameters and pseudo parameters

If no parameter is defined, then only the field's value will be passed to the callback as a parameter.

If any parameters are defined, those are what are passed to the callback.

Because you may need to work with context-specific parameters, the following pseudo parameters are available for any rule, filter or trigger:

Parameter string	|	What is passed
--------------------|-----------------------
`:value`			|	The field's value
`:field`			|	That field object. Can also be a form/subform
`:parent`			|	That field's parent
`:form`				|	The topmost parent
`:alias`			|	The field's alias

[See pseudos section for more on formo pseudos](formo.pseudos)

Note that if you define any parameter, value is no longer passed by default and has to be specified. For example:

	$form->rules('myfield', 'preg_match', array('/[a-z]+/', ':value'));
	
The in the validation messages, the names of additional parameters follows the same rules as Kohana's Validate. That is, the name of the parameter is the value of the parameter.

If, like in the example with preg_match above, the :param replacement doesn't fit, you can make they parameter's key a readable name.

Like this:

	$form->rules('name', 'preg_match', array('all lowercase' => '/[a-z]+/', ':value'));
	
And then the message file could say

	'preg_match'	=> ':field must be :param1';
	
## Message files
The default message file for your validation error messages is specified in `config/formo.php` as `'message_file'`. You can change this to whichever file you need to be your default file.

If you need to use custom files at a form, subform or even field level, set it's `'message_file'` parameter to what you need it to be.

Examples of setting specific message files:

	$form->set('message_file', 'custom_file');
	
	$form->add('username', array('message_file' => 'user_messages'));
	
	$form->username->set('message_file', 'custom_file');
	
## Parameter

## Filters

A filter is a callback that processes a value before setting it as a field's value. A good example of this is stripping a phone number of all non-digit characters.

Filters attached at the form or subform level apply to every one of its fields.

This adds the "trim" filter without any parameters to the form. this will be applied to all fields within the form
	
	$form->filters(NULL, 'trim', NULL);

Here, "trim" will be run only on the username field

	$form->filters('username', 'trim', NULL);
	$form->username->filters(NULL, 'trim', NULL);

## Display Filters
Display filters function exactly like filters but are run on field values only on the rendering object passed into views. Basically, these keep data pretty for the end user.

A good example of a post filter is reformatting a phone number to (xxx) xxx-xxxx format in the view files.

This runs the function "Format::phone($field_value, '(3) 3-4')" on the field, _phone_

	$form->display_filters('phone', 'Format::phone', array(':value', '(3) 3-4'));
	
You can also add display_filters with a group of other rules

$form->rules('phone', array(
	Formo::filter('trim');
	Formo::rule('phone');
	Formo::display_filter('phone', array(':value', '(3) 3-4'))
));
	
## Rules

A rule returns TRUE if the field passes it, and FALSE if it doesn't. By default, a field's value is passed as a sole parameter, but this can be overridden to anything and in any order.

Rules 

## Converting Validate rules to Formo-style rules

The two certainly look the same. The one area you will run into issues is Formo only assumes the first rule is a field's value if no params were defined. If any params are defined, then the param that is value must be specifically defined as well.

Take a look at these examples:

	// Validate rules
	'max_length' => array(32)
	'min_length' => array(3)
	
	// In Formo
	'max_length' => array(':value', 32)
	'min_length' => array(':value', 32)
	
This is implemented so you don't always have to make your validate methods require value to be the first parameter. Then simple functions like preg_match can easily be validated against:

	// Validate requires special regex method
	'regex' => array('/^[\pL_.-]+$/ui')
	
	// But Formo allows you to just use preg_match
	'preg_match'	=> array('/^[\pL_.-]+$/ui', ':value')
	
## Adding objects

Since rules and filters ultimately become rule and filter objects respectively, you can add objects directly into any method that adds validator items and it will be added in the proper spot.

This flexibility makes it nice to group all validation rules together.

Here are some examples:

	$form = Formo::form()
		->add('username')
		->rules('username', Formo::rule('not_empty'))
		->add('email')
		->rules('email', array(
			Formo::filter('trim'),
			Formo::rule('not_empty'),
			Formo::rule('email')
		));
		
This does the same thing but lets you group all rules for every field together in an array.
		
	$form = Formo::form()
		->add('username')
		->add('email')
		->rules(array(
			'username' => array(
				Formo::rule('not_empty')
			),
			'email'	=> array(
				Formo::filter('trim'),
				Formo::rule('not_empty'),
				Formo::rule('email'),
			)
		));