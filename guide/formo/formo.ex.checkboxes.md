# Select examples

## Add a select box

	$hobbies = array
	(
		'Running' => 'run',
		'Swimming' => 'swim',
		array
		(
			'alias' => 'Biking',
			'value' => 'bike'
		),
		'Hiking' => 'hike',
	);
	
	$last_selection = 'swim';

	$form
		->add_group('hobby', 'checkboxes', $hobbies, array('swim', 'hike'), array('label' => 'Favorite Hobbies'));