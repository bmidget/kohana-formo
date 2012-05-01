# List of convenience methods

Because Formo uses `__get()` for retrieving fields instead of variables, the following convenience methods provide direct access to specific protected variables.

Method Name									|	Action
--------------------------------------------|------------|------------------------------------
`fields()`									|	Returns all fields inside a container
`val([$value])`								|	Returns or sets value
`error([$msg])`								|	Returns or sets error message
`errors([$file], [$translate = TRUE])`		|	Returns or sets group error messages
`alias([$alias])`							|	Returns or sets a field's alias