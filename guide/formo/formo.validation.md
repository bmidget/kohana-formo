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

Here are some examples:

	// Attach error messages and check if form passes validation, and require the form to be sent
	if ($form->load()->validate())

	// Attach error messages and check if form passes validation even if it wasn't sent
	if ($form->validate(FALSE))

# Formo validation

Formo provides its own validation system instead of Kohana's packaged Validate library except that it utilizes Validate's helper methods where useful.

Validation is based on filters, rules, triggers and post_filters attached to forms, subforms and individual fields.

## Validation contexts

Formo utilizes Kohana's built-in validation and thus follows the same rules for its definitions.

In addition to the regular `:field`, `:value` and `:validation` parameters, Formo binds the following values as well to the validation object:

Parameter string	|	What is passed
--------------------|-----------------------
`:form`				|	The parent form
`:model`			|	The model object (only applicable when using ORM)

## 'Required' and the 'not_empty' rule

Kohana's Validation class uses a special rule to make a field be required. The rule is `not_empty`. You can add this rule the same as any other rule to your form and fields, but you can also set the same rule up by using the shortcut parameter `required`:

	$form = Formo::form()
		->add('email', array('type' => 'email', 'required' => TRUE))

## Form/subform-level rules

Rules that exist at the form or subform level have the following difference from rules at the field level:

- They are added to the form/subform by their alias
- `:value` passes an array of all the values within the subform

An example of a rule at the form level could be a login form where `username` and `password` have rules that they can't be empty, and the form has a rule that both fields together are correct login credentials.

	$form = Formo::form(array('rules' => array(array('Model_User::can_login', array(':values', 'username', 'password'))))
		->add('username', array('rules' => array(array('not_empty'))))
		->add('password', 'password', array('rules' => array(array('not_empty'))))
		->add('submit', 'submit');

	if ($form->load($_POST)->validate())
	{

	}

In this example, the `username` and `password` fields are first required to not be empty, then the form login rule would run after the other two fields passed.

The example validation rule in `Model_User`:

	public static function can_login($values, $username, $password)
	{
		if (Auth::instance()->login($values[$username], $values[$password]))
			return TRUE;

		return FALSE;
	}