Form Types
==========

Formo uses a decorator system to give extended functionality to forms based on the kind of data it will be handling.

A good example of this a *form type* is **html**. An **html** form needs to render its input fields as html inputs, texteareas, buttons, etc.

### Determining the form type

The default form type is defined in **config/formo.php** as `type`.

If you need to create the form with a specific type form the beginning besides the default one, you can set it with `type`:

	$form = Formo::factory(array('type' => 'json'));
	
If you need to change at any point thereafter, use the `type()` method:

	$form->type('json');