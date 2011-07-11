Working with Kohana's official ORM module
=========================================

You can directly work with ORM model fields inside Formo throught it's `orm()` interface. This interface runs a method on an orm object.

### Load

To load fields from a model, use `orm('load',  $model)`. For instance, if you have a `$user` object and want to convert the record to a Formo form, you can simply do this:

	$user = ORM::factory('user', 20);
	
	$form = Formo::form()
		->orm('load', $user);
		
The `load` method will recognize all rules defined inside the ORM model definition and they will also carry into the form as validation rules.

### Rules in the model

You should only define rules that apply to the model inside the model, and any other rules that may be associated with a form field inside Formo. To define rules inside the model, use the `rules()` method just like normal.

Any rules inside the model will carry over into the form as well.

### Formo definitions in the model

Because it's best practice to do things once, your field-specific definitions for models converted to Formo forms, you will likely want to declare Formo-specific definitions inside the model as well.

The method to make these definitions in the model is `formo()`. You can return an array of any settings inside this method.

	// Inside the model
	// Notice the declaration must be public
	public function formo()
	{
		return array
		(
			'user_tokens' => array
			(
				'render' => FALSE,
			),
			'notes' => array
			(
				'driver' => 'textarea'
			),
		);
	}
	
	
### Flow of using ORM driver with records

The general flow goes like this. You first create the record and load its values and rules into the form. This can also be an empty, unsaved record.

	$user = ORM::factory('user');
	
	$form = Formo::form()
		->orm('load', $user);

Any form-specific definitions are added:

	$form
		->add('confirm', 'password', array('rules' => array(array('matches', array(':validation', 'confirm', 'password')))))
		->add('save', 'submit');

The form is validated, and the record is saved
	
	if ($form->load($_POST)->validate())
	{
		$user->save();
	}

[!!] Note that the values are loaded to the model in the `load()` method as well.

### Has many through and has one relationships

Currently, Kohana's ORM automatically adds and removes relationships the moment `add()` and `remove()` are run and therefore empty records break when doing this.

Because of this ORM shortcoming, Formo will stash relationship changes inside its ORM driver, and you must run the `'save_rel'` method after saving the model.

	if ($form->load($_POST)->validate())
	{
		$user->save();
		$form->orm('save_rel', $user);
	}

### Only pulling certain fields from the model into the form

There are two optional parameters in the `formo::orm('load')` method. They are:

1. an array of field aliases
2. A boolean flag whether those aliases are fields to skip (default is `FALSE`)

If you list an array of fields in the first optional parameter, only those fields will be pulled from the model into the form. But if you set the `skip_fields` flag to `TRUE`, those fields defined in the first optional parameter become a list of fields to skip.

#### Examples of pulling certain fields

How to only pull the `username` and `password` fields:

	$user = ORM::factory('user', 20);
	
	$form = Formo::form()
		->orm('load', $user, array('username', 'password'));

How to pull every field except the password and email fields:

	$user = ORM::factory('user', 20);
	
	$form = Formo::form()
		->orm('load', $user, array('password', 'email'), TRUE);