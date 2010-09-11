Values
==========

Formo assigns and tracks a value for each field. It is this value that is validated against. It is also this value you will pass along to your ORM objects.

Though they will mimick each other, do not confuse the field's value with an HTML input's `value` attribute. They are different things, and the attribute is actually manually set by a driver just prior to rendering a field.

A field's value may be a string or an array depending on the field type. For instance, text fields use string values, and a group of checkboxes would depend on an array of values.

### Setting and retrieving values

**Do not directly access a field's `value` parameter. You must always use `val()` to set and retrieve the value, or you will end up with unexpected results.**

To set a field's value, use `val($value)`:

	$form->email->val('john@doe.com');

To retrieve a field's value, use `val()`:

	$posted_email = $form->email->val();
