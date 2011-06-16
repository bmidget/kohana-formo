# Getting to know Formo, your new best friend

The purpose of Formo is to make working with forms object-oriented. Thus, the "o" in "Formo" stands for "object".

Specifically, a Formo form is understood as the following:

1. Data in
2. Data validated
3. Data out

Data in will be sent to formo as raw data. This could be POST, GET, plain XML, JSON, anything. Formo acts as a hub for data validation using Kohana's built-in Validate library.

Data out is any sort of response. Perhaps JSON for an AJAX request, perhaps pure XML in a SOAP response, and, of course an HTML form for most HTML requests.

Additionally, Formo can act as a useful hub for data. A good example of this is data interaction with a model. The model provides the tools for interacting with the database, but Formo provides the tools for displaying the method for inputting the data as well as the appropriate response to a request.

## Structure

This document explains the structure of a Formo object and gives basic information about each part. The heirarchy of an object looks like this:

- Formo
- Container
	- Validator
		- Form/Field
			- ORM
			- Driver
				- View
			
### Formo

This class acts as an interface to Formo objects.

### Container

The container class handles the ability for a form or field to contain other fields or forms within itself and find them. It also understands how to access the driver and orm objects.

### Validator

The validator class carries the ability to add and remove rules as well as how to run them. This class also keeps track of errors.

### Form/Field

Although both forms and fields have the ability to carry fields and forms within themselves because of the container object, each of these classes hold specific functionality for dealing with forms, subforms and fields specifically.

### ORM

The ORM object acts as a driver and interface to connectivity with an ORM library

### Driver

Drivers handle form and field-specific functionality. For instance, a group of checkbox fields is checked for being not_empty differently from a password field.

### View

The view adds extra functionality to a form or field object. An example of a decorator is the HTML decorator that adds `attr()` and `add_class()` and also renders forms and fields as HTML DOM objects.

[Continue to Getting Started >](formo.getting-started)