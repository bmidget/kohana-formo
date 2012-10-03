<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests the Arr lib that's shipped with kohana
 *
 * @group formo
 * @group formo.formo
 *
 * @package    Formo
 * @category   Tests
 */
class Formo_FormoTest extends Unittest_TestCase {

	/**
	 * Provides test data for test_form()
	 *
	 * @return array
	 */
	public function provider_form()
	{
		return array(
			// No alias
			array(array(), array('alias' => 'formo', 'driver' => 'form')),
			// Alias set
			array(array('alias' => 'foo'), array('alias' => 'foo', 'driver' => 'form')),
			// Keyless construct
			array
			(
				array('foo', 'group', null, array('attr.method' => 'GET')),
				array('alias' => 'foo', 'driver' => 'group', 'val' => null, 'attr' => array('class' => NULL, 'method' => 'GET', 'id' => 'foo'))
			),
		);
	}

	/**
	 * @dataProvider provider_form
	 * @param string $page  Page name passed in the URL
	 * @param string $expected_file  Expected result from Controller_Userguide::file
	 */
	public function test_form( array $args, array $array)
	{
		$expected = TRUE;
		$result = TRUE;

		$form = Formo::form($args);

		foreach ($array as $key => $value)
		{
			if ($form->get($key) !== $value)
			{
				$result = FALSE;
			}
		}

		$this->assertSame($expected, $result);
	}


}