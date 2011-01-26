# Migrating to Formo for Kohana 3.1.x

As Formo nears its Dot-oh API, there have been a few API changes you'll have to fix to upgrade your application to this version of Formo that's compatable with Kohana 3.1.

## Validation

Formo now utilizes Kohana's core validation library for all validation, and all definitions now follow those guidelines.

### Rule definitions

Rule definitions have changed in Kohana 3.1, and Formo follows suit. Instead of key/value pairs that prepresent. Also, since all validation is done through the Validation library, rule definitions can no longer be `Formo::rule()` objects.

Rules that were previously defined as objects can easily be converted to validation rules:

	Formo::rule('not_empty', NULL)

Becomes

	array('not_empty', NULL)

### Force validation

The option inside the `validate` method is now handled opposite as it was previously. Now the method validates whether or not the form was sent by default and must be passed `TRUE` to validate only if sent:

	if ($form->validate(TRUE))
