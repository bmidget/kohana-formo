# Formo HTML views

At render time, every form and field is passed into view files for rendering. This allows you to wrap fields in HTML tags, or add whatever styling is necessary for you fields, forms and subforms.

Formo makes rendering parts of your form extremely flexible and allows you to mix whatever blend of Object-Oriented and straight-text approaches you prefer in defining your fields.

Forms and subforms are passed to views as the variable `$form`, and fields are passed to views as `$field`.

The view files are based on a prefix system to make generating specific forms easier.

	$form->set('view_prefix', '/specialform')->generate();

## Forms, fields as DOM objects

When rendering a form as HTML, form/subform and field objects are passed to the views in a more usable state as `Formo_Render_html` objects. Think of these objects as **HTML DOM objects**.

These objects are manipulated much like jQuery DOM objects and use similar syntax.
