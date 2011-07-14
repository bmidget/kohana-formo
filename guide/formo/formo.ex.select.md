# Select box examples

## Add a select box

	$hobbies = array
	(
		'Running' => 'run',
		'Swimming' => 'swim',
		array
		(
			'alias' => 'Biking',
			'label' => 'Mountain Biking',
			'value' => 'bike'
		),
		'Hiking' => 'hike',
	);
	
	$last_selection = 'swim';

	$form
		->add_group('hobby', 'select', $hobbies, 'swim', array('label' => 'Favorite Hobby'));