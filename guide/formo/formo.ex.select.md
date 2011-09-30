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

	$form
		->add_group('hobby', 'select', $hobbies, 'swim', array('label' => 'Favorite Hobby'));
		
## With optgroups

	$activities = array
	(
		'Exercise' => array
		(
			'Running' => 'run',
			'Swimming' => 'swim',
			array
			(
				'alias' => 'Biking',
				'label' => 'Mountain Biking',
				'value' => 'bike',
			),
			'Hiking' => 'hike',
		),
		'Dates' => array
		(
			'Movie' => 'movie',
			'Roller Skating' => 'rollerskating',
			'Dinner' => 'dinner',
			'Bowling' => 'bowling',
		)
	);
	
	$form->add('activity', 'select', $initial_value, array('optgroups' => $activities));