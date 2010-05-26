# Getting to know Formo, your new best friend

The purpose of Formo is to make working with forms object-oriented. Thus, the "o" in "Formo" stands for "object".

Specifically, a Formo form is understood as the following:

1. Data in
2. Data validated
3. Data out

Data in will be sent to formo as raw data. This could be POST, GET, plain XML, JSON, anything. Formo acts as a hub for data validation using Kohana's built-in Validate library.

Data out is any sort of response. Perhaps JSON for an AJAX request, perhaps pure XML in a SOAP response, and, of course an HTML form for most HTML requests.

Additionally, Formo can act as a useful hub for data. A good example of this is data interaction with a model. The model provides the tools for interacting with the database, but Formo provides the tools for displaying the method for inputting the data as well as the appropriate response to a request.

[Continue to Getting Started >](formo.getting-started)