Rendering Forms and Fields
==========================

Formo can render fields and forms by either *rendering* them though their predefined view files or with the `html()` method on the object itself.

### Rendering

Each form and field has a driver that defines a view file associated with it. When a field is rendered, the field object is passed into the defined view file.

A *rendered* form or field returns a View object of that field.

A view file may *render* individual parts of a form or field within it.

The `render([$file])` method allows two optional paramters to specify a view file.

### HTML

Form view objects determine how an individual form or object is actually turned into html.

A good example of this is rendering an HTML field element. HTML elements all have

Learn more about types in the [types section](formo.kinds)
