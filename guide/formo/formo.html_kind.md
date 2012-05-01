The HTML Kind
=============

You will likely use the HTML view in your formo forms. This is an overview of the functionality it adds to Formo objects. The view object is passed into the view file as `$this` and is accessible everywhere else with `view()`.

	$view_obj = $form->username->view();

### Open

You will often need to render the HTML DOM object's opening tag only — complete with attributes and styles. In the view:

	echo $this->open();

### Close

Likewise, you will often need to only render the object's closing tag. In the view:

	echo $this->close();

### Render

To render the HTML DOM object, use `html()`. In the view:

	echo $this->html();

For more about rendering forms, see the [rendering section](formo.rendering);

### Render

To render a view file using a field or form object, use `render()`:

	echo $field->render();

For more about generating forms, see the [rendering section](formo.rendering);

### Attributes

HTML attributes, such as `style="height:25px" class="someclass" id="something"`, etc. are easily easily accessed with the HTML decorator through the `attr()`, `css()`, `add_class()`, and `remove_class()` methods.

#### Non-class, non-style attributes

To set an attribute, use `attr($attr_name, $attr_value)`:

	$field->view()->attr('method', 'post');

To retrive an attribute, use `attr($attr_name)`:

	$method = $field->view()->attr('method');

If you want to remove an attribute, set it to `NULL`:

	$field->view()->attr('id', NULL);

If you want the attribute to remain but be empty, use an empty string:

	$field->view()->attr('action', '');

You can set multiple attributes at a time with an array:

	$field->view()->attr(array(
		'method' => 'post',
		'action' => '',
		'id' => 'my-special-form',
	));

#### Styles

To set a style for a HTML DOM element, use `css($style_name, $style_value)`:

	$form->email->view()->css('width', '300px');

To retrive a style, use `css($style_name)`:

	$width = $form->username->view()->css('width');

To remove a style, set it to `NULL`:

	$form->password->view()->css('font-weight', NULL);

You can set multiple styles at a time with an array:

	$form->email->view()->css(array(
		'width' => '200px',
		'background-color' => '#eee',
		'border' => '1px solid #999,
	));

#### Classes

You can set a class or classes using `attr()`, but it will override whatever classes have previously been set:

	$field->view()->attr('class', 'oneclass twoclass');

Use `add_class($classname)` to add a class:

	$form->email->view()->add_class('email');

You can set multiple classes at a time with either an array or space-separated class names:

	$form->email->view()->add_class('email specialfield');
	$form->email->view()->add_class(array('email', 'specialfield'));

Use `remove_class($classname)` to remove a class:

	$form->username->view()->remove_class('sucks');

You can remove multiple classes at a time with either an array or space-separated class names:

	$form->username->view()->remove_class('sucks specialfield');
	$form->username->view()->remove_class(array('sucks', 'specialfield'));

### Labels

Use `label()` to retrieve the field's label. This will return the variable `label` if it's been set, and `alias` if `label` hasn't been set.

	$label = $field->view()->label();

### Text

You can set the inner text of a HTML object with `text($text)`:

	$form->comment->view()->text('Enter your comment here');

You can remove text by setting it to `NULL` or an empty string:

	$form->comment->view()->text(NULL);

Retrieve an object's text with `text()`:

	$comment_text = $field->text();

#### Basic text manipulation of text

To add a string to the end of the object's inner text, use `text('.=', $string)`:

	$field->view()->text('.=', ' - I meant what I said');

To add a string to the beginning of the object's inner text, use `text('=.', $string)`:

	$field->view()->text('=.', 'PAY ATTENTION TO THIS: ');

To run a callback on the object's inner text, use `text('callback', $callback)`:

	$field->view()->text('callback', 'trim');
