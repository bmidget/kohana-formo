# Working with Jelly ORM and Formo

There are two approaches to working with Jelly ORM objects inside Formo:

* Pull and push data from and to the model at the controller level
* Directly tie in Formo through an updated Jelly_Model class

## Pulling and pusing approach

Formo gives a simple interface for pulling information from Jelly fields and incorporating them into the form.

The following example will pull an entire model and create a form from it:

	$user = Jelly::select('user', 25);

	$form = Formo::form()
		->orm('load', $user);
		
	if ($form->load()->validate())
	{
		try
		{
			$user-save();
		}
		catch (Validate_Exception $e)
		{
			// Do somethign with $e
		}
	}	
		
Notice any relationship fields are automatically populated as select lists or checkboxes for BelongsTo, HasMany, HasOne and ManyToMany fields. Also, rules defined and inside the field definitions are included in Formo as well. You can add other information you need inside your Formo object by adding them to field definitions, too, such as:

* A specific Formo driver
* An `attr` array defining HTML attriutes
* A `css` array for defining HTML inline style attributes

You can also pull pieces of a model when you don't need to work with every field. Here's an example of pulling just the username and password:

	$user = Jelly::select('user', 25);
	
	$form = Formo::form()
		->orm('load', $user, array('username, 'password'));
		
	if ($form->load()->validate())
	{
		try
		{
			$user->save();
		}
		catch Validate_Exception $e
		{
			// do something with $e;
		}
	}
	
## Direct integration approach

You can really make Jelly and Formo play nicely together by using the Jelly_Model for integration. When you do this, Jelly will use a Formo object to validate against instead of the Validate library.

With this approach every Jelly model has a property named `$model->form` that is the jelly fields as a Formo object. This is created on the fly only when necessary as not to add any overhead to Jelly when it's not necessary.

Here's an example of working with a user record using this approach:

	$user = Jelly::select('user', 25);
	
	$user->form
		->add('submit', 'submit');
		
	$this->template->content = $user->form->generate();
	
	if ($user->load()->sent())
	{
		try
		{
			// Data is validated at save
			$user->save();
		}
		catch(Validate_Exception $e)
		{
			// Do something with $e
		}
		
	}

Here's an example of a login form and how clean and obvious this tight integration makes working with forms:

	// For the login form, we just need a blank record
	$user = Jelly::factory('user');
	
	// We can pull specific parts of the model by using $model->subform() instead of $model->form
	$user->subform(array('username', 'password'))
		->add('submit', 'submit');
		
	$this->template->content = $user->subform->generate();
	
	// Notice here we are working with just the user form object and not
	// just $user->load()->validate. This is because we are working with
	// a couple fields and not the entire user model
	if ($user->subform->load()->validate())
	{
		if ($user->login())
			$this->request->redirect('admin');
			
		$user->subform->error('invalid_login');
	}
	
If you set up your model correctly, the following would always fail at $user->save()

	$user = Jelly::factory('user');
	
	$user->subform(array('email'))
		->add('submit', 'submit');
		
	$this->template->content = $user->subform->generate();
	
	// Notice loading and validating against the form and not user
	if ($user->subform->load()->validate())
	{
		// This would fail because in a typical user model,
		// username and password would at least be required
		// for a new record
		$user->save();
	}
	
Therefore validation takes place at two levels: the form level, and the model level. This preserves data integrity and allows for separate form logic from model logic.

Also, since Formo pulls its rules directly from the field definitions in the model, both easily share validation parameters.