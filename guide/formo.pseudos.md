## Pseudo context

If you need to specify a context that isn't available until validate time for your rule methods to run against, you can use pseudo contexts. These follow the Kohana 3 convention of being written with a colon (":") followed by the name of the context (ie ":model").

The contexts available are:

* `:model` - For the model object
* `:field` - For the field object. This is also the default context for a rule
* `:parent` - The field's immediate parent
* `:form` - The topmost parent of the form

Syntax for using the pseudo contexts looks like a simple static function. Here is an example inside a Jelly model's initialize method:

	->fields(array(
		'username'	=> array
		(
			'rules'	=> array
			(
				':model::myrule' => array(25),
				':field::another_rule'	=> NULL,			
			)
		),
	))

In this case, this method is run as a Validator rule:

	$model->myrule(25);
	$field->another_rule($field->val());
	
The nice thing about this is you get to work with the entire model instance instead of just a static method inside the rule method.

## Pseudo params

Pseudo params work much like pseudo contexts for rules, only they are stings that refer to parameters that are only available when validate is called. The same pseudo parameters exist with the addition of ":value" and ":alias":

* `:value` - The field's value
* `:alias` - The field's alias
* `:field` - The field object
* `:parent` - The field's immediate parent
* `:form` - The topmost parent of the form
* `:model` - The model

With pseudo params, you can easily pass whatever you need to adequately perform complex rule checks.

Note that unlike Kohana's Validate, Formo's Validator does force you to pass parameters in a specific order by convention. If no parameters are explicitly stated, the field's value is passed as the sole parameter.

If you define any parameter, you will have to specify which parameter is the field's value as well.

Example:

	'rules' => array
	(
		'not_empty' => NULL,
		'max_length' => array(':value', 32),
		'preg_match' => array('/[a-z]+/', ':value'),s
	);