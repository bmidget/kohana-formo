<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests the Formo module
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
		return array
		(
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

	public function provider_alias()
	{
		return array
		(
			array('melmon', 'melmon')
		);
	}

	/**
	 * @dataProvider provider_alias
	 */
	public function test_alias($alias, $expected)
	{
		$field = Formo::factory(array(
			'alias' => $alias,
		));

		$result = $field->alias();

		$this->assertSame($expected, $result);
	}

	public function provider_addRule()
	{
		return array
		(
			array
			(
				array('not_empty'),
				array(array('not_empty')),
			),
			array
			(
				array
				(
					':self' => array
					(
						array('not_empty'),
						array('matches', array(':form_val', 'foo1', 'bar1')),
					)
				),
				array
				(
					array('not_empty'),
					array
					(
						'matches',
						array(':form_val', 'foo1', 'bar1'),
					),
				),
			),
		);
	}

	/**
	 * @dataProvider provider_addRule
	 */
	public function test_addRule( array $array, $expected)
	{
		$field = Formo::factory(array('alias' => 'foo'))
			->add_rule($array);

		$rules = $field->get('rules');

		$this->assertSame($expected, $rules);
	}

	public function provider_attr()
	{
		return array
		(
			// Setting an array
			array
			(
				array('foo'),
				array(':self' => array('class' => 'specialclass', 'onclick' => 'go()')),
				NULL,
				array('class' => 'specialclass', 'onclick' => 'go()'),
			),
			// Unsetting attribute
			array
			(
				array('alias' => 'foo', 'attr.onclick' => 'good_dog()'),
				'onclick',
				NULL,
				array('onclick' => NULL),
			),
		);
	}

	/**
	 * @dataProvider provider_attr
	 */
	public function test_attr( array $construct, $attr, $val, array $checks)
	{
		$expected = TRUE;
		$result = TRUE;

		$field = Formo::factory($construct);

		if (is_array($attr))
		{
			$field->attr($attr);
		}
		else
		{
			$field->attr($attr, $val);
		}

		foreach ($checks as $key => $value)
		{
			if ($field->attr($key) !== $value)
			{
				$result = FALSE;
			}
		}

		$this->assertSame($expected, $result);
	}

}