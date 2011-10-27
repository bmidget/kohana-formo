# Radio group examples

## Add a radio group

	$hobbies = array
	(
		'run' => 'Running',
		'swim' => 'Swimming',
		'bike' => 'Biking',
		'hike' => 'Hiking',
	);
	
	$last_selection = 'swim';

	$form
		->add_group('hobby', 'radios', $hobbies, 'swim', array('label' => 'Favorite Hobby'));