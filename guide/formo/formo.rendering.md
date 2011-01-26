Rendering Forms and Fields
==========================

Formo can render fields and forms by either *generating* them though their predefined view files or by *rendering* the object itself.

### Generating

Each form and field has a driver that defines a view file associated with it. When a field is generated, the field object is passed into the defined view file.

A *generated* form or field returns a View object of that field.

A view file may *render* individual parts of a form or field within it.

The `generate([$view[, $prefix]])` method allows two optional paramters to specify a view file or prefix.

#### View prefixes

Formo view files can be prefixed. Prefixed sets of view files allow for easy form templating.

For instance, your website may use a different style of forms in the visitors section and the members area. If you create two prefixed sets of fields for your site (visitors and members), you could easily specify which template to generate by specifying the view prefix.

	$form->generate(FALSE, 'visitors');

Otherwise, the *view_prefix* is set as `view_prefix`:

	$form = Formo::factory(array('view_prefix', 'some/path'));
	$form->set('view_prefix', 'some/path');

Prefixes are understood recursively inside fields within fields.

#### View file

As described earlier, a view file handles the generated form. A object variable is passed to the view file for generating your views for the form.

**Formo will always assume you are specifying a view file within a view prefix unless the prefix is set to `FALSE`.**

To specify a view file other than the driver's default:

	$form->generate('view_file');

To specigy a view file not within any *view_prefix*:

	$form->generate('view_file', FALSE);

### Rendering

Form decorators determine how an individual form or object is actually rendered.

A good example of this is rendering an HTML field element. HTML elements all have

Learn more about types in the [types section](formo.types)
