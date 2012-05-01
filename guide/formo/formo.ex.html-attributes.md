# Definining HTML Attributes

HTML attributes are attributes inside the HTML tag. You often need to define these different attributes when working with forms.

### What are HTML attributes?

We'll use the following field as our example:

	<input type="email" name="email" onclick="method" placeholder="some text" class="someclass" id="someid" />

In Formo, the attribute `name="email"` and `type="email"` are defined elsewhere, but the other general attributes are defined as `attr`.

### Define attributes outside the view

At creation in the options array

	$form->add('email', 'email', array('type' => 'email', 'attr' => array('onclick' => 'method', 'id' => 'someid', 'class' => 'someclass')));
	
Using `set`

	$form->email->set('attr', array('onclick' => 'method', 'id' => 'someid', 'class' => 'someclass'));

### Define attributes inside the view

	$this->attr('id', 'someid')->attr('onclick', 'method')->add_class('someclass');