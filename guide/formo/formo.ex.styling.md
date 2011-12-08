# Styling example

By default, Formo uses html files that build the inputs in a specific way. Here is an example of a stylesheet that works with formo:

	form {
		color:#000000;
		font-size:.9em;
	}
	
	form label .label {
		display:block;
		font-weight:bold;
		text-transform:capitalize;
	}
	
	form label {
		display:block;
		font-size:14px;
	}
	
	form input[type="text"],
	form input[type="password"],
	form input[type="email"],
	form input[type="color"],
	form input[type="tel"],
	form input[type="date"],
	form input[type="datetime"],
	form input[type="datetime-local"],
	form input[type="week"],
	form input[type="time"],
	form input[type="month"],
	form input[type="range"],
	form input[type="tel"],
	form textarea,
	form select {
		border:1px solid #dddddd;
		padding:5px;
		width:300px;
	}
	
	form p.bool .label {
		display:inline;
		font-weight:normal;
	}
	
	form textarea {
		  font-family:Helvetica, Arial, sans-serif;
	}
	form .error input[type="text"],
	form .error input[type="password"],
	form .error input[type="email"],
	form .error input[type="color"],
	form .error input[type="tel"],
	form .error input[type="date"],
	form .error input[type="datetime"],
	form .error input[type="datetime-local"],
	form .error input[type="week"],
	form .error input[type="time"],
	form .error input[type="month"],
	form .error input[type="range"],
	form .error input[type="tel"],
	form .error textarea,
	form .error select {
		  border-color:red;
	}
	
	form .checkboxes span.label,
	form .radios span.label {
		font-weight:bold;
		text-transform:capitalize;
	}
	
	form span.field {
		  display:block;
	}
	
	form span.error-message {
		display:block;
		color:red;
		text-transform: capitalize;
	}
	
	form span.radio {
		display:block;
		margin:5px 0;
	}
	
	form .radio label {
		  font-weight:normal;
	}
	
	span.error-message {
		display:block;
		color:red;
	}
	
	form h2 {
		text-transform: capitalize;
		font-size: 18px;
	}
