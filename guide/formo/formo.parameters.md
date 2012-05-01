# Useful field parameters

The following is a list of parameters you will often deal with.

Parameter		|	Type	|	Function
----------------|-----------|------------
`editable`		|	Bool	|	When `FALSE`, field value displays as text instead of input. When `TRUE` it is displayed as a field.
`render`		|	Bool	|	If set to `FALSE`, Formo will not render or validate the field
`ignore`		|	Bool	|	If set to `TRUE`, Formo will ignore the field's validate rules
`attr`			|	Array	|	Carried over into forms/fields rendered as HTML. These are `key => value` pairs of HTML attribute tags
`css`			|	Array	|	Carried over into forms/fields rendered as HTML. These are `key => value` pairs of the `style` attribute tag
`label`			|	String	|	Becomes a field's label. If not specified, the alias is its label
`driver`		|	String	|	The driver that handles the field/form type
`options`		|	Array	|	Become available options for selects, radio groups and checkbox groups
`order`			|	Mixed	|	Specifies where the field is placed relative to other fields

## Setting specific parameters

### Value

To set/retrive a field's **value**, use `val()`:

	// Sets the value to "my_username"
	$form->username->val('my_username');
	
	// Returns "my_username"
	$form->username->val();
	
### Alias

	// Sets the alias to "special_form"
	$form->alias('special_form');
	
	// Returns "special_form"
	$form->alias();