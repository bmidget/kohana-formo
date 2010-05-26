# The DOM HTML object

When rendering any Formo object as HTML, a special object is passed to the views to make working with a Formo form or field as a native HTML object.

Rendering any field is view-based, but it's your choice whether to work with items in your view files as objects or as strict text.

At times it makes the most sense to pass an entire form object to a page's view file where it is rendered.

	$form = Formo::factory()
		->add('username')
		->add('email');
	
	$this->template->content = View::factory('some/view/file')
		->bind('form', $form);
		
In a case like this you may not want the form to be auto-generated. Your form may have a layout like this:

* explanation text
* username
* explanation text
* email
	
Formo makes it easy to make a custom form on the fly. Also, you can work with form parts as objects instead of individual view files too.

	<?=$form->open();?>
	
	<div class="explanation">This is some explanation text</div>
	<div class="form-item"><?=$form->username->add_class('myclass')->css('width', '400px')?></div>
	
	<div class="explanation">This is another explanation</div>
	<div class="form-item"><?=$form->email->add_class('myclass emailinput')->css('width', '300x')?></div>
	
	<input type="submit" name="submit" value="Submit" />
	
	<?=$form->close();?>