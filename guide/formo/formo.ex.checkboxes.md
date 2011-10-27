# Select examples

## Add a select box

	$hobbies = array
	(
		'run' => 'Running',
		'swim' => 'Swimming',
		'bike' => 'Biking',
		'hike' => 'Hiking',
	);
	
	$last_selection = 'swim';

	$form
		->add_group('hobby', 'checkboxes', $hobbies, array('swim', 'hike'), array('label' => 'Favorite Hobbies'));