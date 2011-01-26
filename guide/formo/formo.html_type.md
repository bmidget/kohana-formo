The HTML Type
=============

You will likely use the HTML decorator in your formo forms. This is an overview of the functionality it adds to Formo objects.

### Open

You will often need to render the HTML DOM object's opening tag only — complete with attributes and styles.

	echo $form->open();

### Close

Likewise, you will often need to only render the object's closing tag:

	echo $form->close();

### Render

To render the HTML DOM object, use `render()`:

	echo $field->render();

For more about rendering forms, see the [rendering section](formo.rendering);

### Generate

To generate a view file using a field or form object, use `generate()`:

	echo $form->generate();

For more about generating forms, see the [rendering section](formo.rendering);

### Attributes

HTML attributes, such as `style="height:25px" class="someclass" id="something"`, etc. are easily easily accessed with the HTML decorator through the `attr()`, `css()`, `add_class()`, and `remove_class()` methods.

#### Non-class, non-style attributes

To set an attribute, use `attr($attr_name, $attr_value)`:

	$form->attr('method', 'post');

To retrive an attribute, use `attr($attr_name)`:

	$method = $form->attr('method');

If you want to remove an attribute, set it to `NULL`:

	$form->attr('id', NULL);

If you want the attribute to remain but be empty, use an empty string:

	$form->attr('action', '');

You can set multiple attributes at a time with an array:

	$form->attr(array(
		'method' => 'post',
		'action' => '',
		'id' => 'my-special-form',
	));

#### Styles

To set a style for a HTML DOM element, use `css($style_name, $style_value)`:

	$form->email->css('width', '300px');

To retrive a style, use `css($style_name)`:

	$width = $form->username->css('width');

To remove a style, set it to `NULL`:

	$form->password->css('font-weight', NULL);

You can set multiple styles at a time with an array:

	$form->email->css(array(
		'width' => '200px',
		'background-color' => '#eee',
		'border' => '1px solid #999,
	));

#### Classes

You can set a class or classes using `attr()`, but it will override whatever classes have previously been set:

	$form->attr('class', 'oneclass twoclass');

Use `add_class($classname)` to add a class:

	$form->email->add_class('email');

You can set multiple classes at a time with either an array or space-separated class names:

	$form->email->add_class('email specialfield');
	$form->email->add_class(array('email', 'specialfield'));

Use `remove_class($classname)` to remove a class:

	$form->username->remove_class('sucks');

You can remove multiple classes at a time with either an array or space-separated class names:

	$form->username->remove_class('sucks specialfield');
	$form->username->remove_class(array('sucks', 'specialfield'));

### Labels

Use `label()` to retrieve the field's label. This will return the variable `label` if it's been set, and `alias` if `label` hasn't been set.

	$label = $field->label();

### Text

You can set the inner text of a HTML object with `text($text)`:

	$form->comment->text('Enter your comment here');

You can remove text by setting it to `NULL` or an empty string:

	$form->comment->text(NULL);

Retrieve an object's text with `text()`:

	$comment_text = $field->text();

#### Basic text manipulation of text

To add a string to the end of the object's inner text, use `text('.=', $string)`:

	$field->text('.=', ' - I meant what I said');

To add a string to the beginning of the object's inner text, use `text('=.', $string)`:

	$field->text('=.', 'PAY ATTENTION TO THIS: ');

To run a callback on the object's inner text, use `text('callback', $callback)`:

	$field->text('callback', 'trim');
