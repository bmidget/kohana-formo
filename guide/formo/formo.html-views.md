# Formo HTML views

At render time, every form and field is passed into view files for rendering. This allows you to wrap fields in HTML tags, or add whatever styling is necessary for you fields, forms and subforms.

Formo makes rendering parts of your form extremely flexible and allows you to mix whatever blend of Object-Oriented and straight-text approaches you prefer in defining your fields.

Forms, subforms and field view objects are accessible to views as the variable `$this`.

The view files are based on a prefix system to make generating specific forms easier.

	$form->set('view_prefix', '/specialform')->render();

## Forms, fields as DOM objects

When rendering a form as HTML, form/subform and field objects are passed to the views in a more usable state as `Formo_View_HTML` objects. Think of these objects as **HTML DOM objects**.

These objects are manipulated much like jQuery DOM objects and use similar syntax.

[!!] Note that the view file can access its view object with `$this` context`.