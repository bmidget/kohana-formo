# Select box examples

## Add a select box

	$hobbies = array
	(
		'run' => 'Running',
		'swim' => 'Swimming',
		'bike' => 'Biking',
		'hike' => 'Hiking',
	);

	$form
		->add_group('hobby', 'select', $hobbies, 'swim', array('label' => 'Favorite Hobby'));
		
## With optgroups

	$activities = array
	(
		'Exercise' => array
		(
			'run' => 'Running',
			'swim' => 'Swimming',
			'bike' => 'Biking',
			'hike' => 'Hiking',
		),
		'Dates' => array
		(
			'movie' => 'Movie',
			'rollerskating' => 'Roller Skating',
			'dinner' => 'Dinner',
			'bowling' => 'Bowling',
		)
	);
	
	$form->add('activity', 'select', $initial_value, array('optgroups' => $activities));