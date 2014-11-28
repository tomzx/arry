<?php

namespace Arry;

class ArryTest extends TestCase
{
	public function testArrayBuild()
	{
		$this->assertEquals(array('foo' => 'bar'), Arry::build(array('foo' => 'bar'), function($key, $value)
		{
			return array($key, $value);
		}));
	}

	public function testArrayDot()
	{
		$array = Arry::dot(array('name' => 'taylor', 'languages' => array('php' => true)));
		$this->assertEquals($array, array('name' => 'taylor', 'languages.php' => true));
	}

	public function testArrayGet()
	{
		$array = array('names' => array('developer' => 'taylor'));
		$this->assertEquals('taylor', Arry::get($array, 'names.developer'));
		$this->assertEquals('dayle', Arry::get($array, 'names.otherDeveloper', 'dayle'));
		$this->assertEquals('dayle', Arry::get($array, 'names.otherDeveloper', function() { return 'dayle'; }));
	}

	public function testArraySet()
	{
		$array = array();
		Arry::set($array, 'names.developer', 'taylor');
		$this->assertEquals('taylor', $array['names']['developer']);
	}
	public function testArrayForget()
	{
		$array = array('names' => array('developer' => 'taylor', 'otherDeveloper' => 'dayle'));
		Arry::forget($array, 'names.developer');
		$this->assertFalse(isset($array['names']['developer']));
		$this->assertTrue(isset($array['names']['otherDeveloper']));
		$array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle', 'thirdDeveloper' => 'Lucas']];
		Arry::forget($array, ['names.developer', 'names.otherDeveloper']);
		$this->assertFalse(isset($array['names']['developer']));
		$this->assertFalse(isset($array['names']['otherDeveloper']));
		$this->assertTrue(isset($array['names']['thirdDeveloper']));
		$array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas', 'otherDeveloper' => 'Graham']];
		Arry::forget($array, ['names.developer', 'otherNames.otherDeveloper']);
		$expected = ['names' => ['otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas']];
		$this->assertEquals($expected, $array);
	}

	public function testArrayPluckWithArrayAndObjectValues()
	{
		$array = array((object) array('name' => 'taylor', 'email' => 'foo'), array('name' => 'dayle', 'email' => 'bar'));
		$this->assertEquals(array('taylor', 'dayle'), Arry::pluck($array, 'name'));
		$this->assertEquals(array('taylor' => 'foo', 'dayle' => 'bar'), Arry::pluck($array, 'email', 'name'));
	}

	public function testArrayExcept()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('age' => 26), Arry::except($array, array('name')));
	}

	public function testArrayOnly()
	{
		$array = array('name' => 'taylor', 'age' => 26);
		$this->assertEquals(array('name' => 'taylor'), Arry::only($array, array('name')));
		$this->assertSame(array(), Arry::only($array, array('nonExistingKey')));
	}

	public function testArrayDivide()
	{
		$array = array('name' => 'taylor');
		list($keys, $values) = Arry::divide($array);
		$this->assertEquals(array('name'), $keys);
		$this->assertEquals(array('taylor'), $values);
	}

	public function testArrayFirst()
	{
		$array = array('name' => 'taylor', 'otherDeveloper' => 'dayle');
		$this->assertEquals('dayle', Arry::first($array, function($key, $value) { return $value == 'dayle'; }));
	}

	public function testArrayLast()
	{
		$array = array(100, 250, 290, 320, 500, 560, 670);
		$this->assertEquals(670, Arry::last($array, function($key, $value) { return $value > 320; }));
	}

	public function testArrayFetch()
	{
		$data = array(
			'post-1' => array(
				'comments' => array(
					'tags' => array(
						'#foo', '#bar',
					),
				),
			),
			'post-2' => array(
				'comments' => array(
					'tags' => array(
						'#baz',
					),
				),
			),
		);
		$this->assertEquals(array(
			0 => array(
				'tags' => array(
					'#foo', '#bar',
				),
			),
			1 => array(
				'tags' => array(
					'#baz',
				),
			),
		), Arry::fetch($data, 'comments'));
		$this->assertEquals(array(array('#foo', '#bar'), array('#baz')), Arry::fetch($data, 'comments.tags'));
	}

	public function testArrayFlatten()
	{
		$this->assertEquals(array('#foo', '#bar', '#baz'), Arry::flatten(array(array('#foo', '#bar'), array('#baz'))));
	}

	public function testArraySort()
	{
		$array = array(
			array('name' => 'baz'),
			array('name' => 'foo'),
			array('name' => 'bar'),
		);
		$this->assertEquals(array(
			array('name' => 'bar'),
			array('name' => 'baz'),
			array('name' => 'foo')),
		Arry::values(Arry::sort($array, function($v) { return $v['name']; })));
	}

	public function testArrayAdd()
	{
		$this->assertEquals(array('surname' => 'Mövsümov'), Arry::add(array(), 'surname', 'Mövsümov'));
		$this->assertEquals(array('developer' => array('name' => 'Ferid')), Arry::add(array(), 'developer.name', 'Ferid'));
	}

	public function testArrayPull()
	{
		$developer = array('firstname' => 'Ferid', 'surname' => 'Mövsümov');
		$this->assertEquals('Mövsümov', Arry::pull($developer, 'surname'));
		$this->assertEquals(array('firstname' => 'Ferid'), $developer);
	}
}