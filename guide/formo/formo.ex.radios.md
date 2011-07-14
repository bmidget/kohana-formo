# Radio group examples

## Add a radio group

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
		->add_group('hobby', 'radios', $hobbies, 'swim', array('label' => 'Favorite Hobby'));