<?php

namespace Arry\Test;

use Arry\Arry;

class ArryTest extends TestCase
{
    public function testArrayBuild()
    {
        $this->assertEquals(['foo' => 'bar'], Arry::build(['foo' => 'bar'], function ($key, $value) {
            return [$key, $value];
        }));
    }

    public function testArrayDot()
    {
        $array = Arry::dot(['name' => 'taylor', 'languages' => ['php' => true]]);
        $this->assertEquals($array, ['name' => 'taylor', 'languages.php' => true]);
    }

    public function testArrayGet()
    {
        $array = ['names' => ['developer' => 'taylor']];
        $this->assertEquals('taylor', Arry::get($array, 'names.developer'));
        $this->assertEquals('dayle', Arry::get($array, 'names.otherDeveloper', 'dayle'));
        $this->assertEquals('dayle', Arry::get($array, 'names.otherDeveloper', function () {
            return 'dayle';
        }));
    }

    public function testArrayHas()
    {
        $array = ['names' => ['developer' => 'taylor']];
        $this->assertTrue(Arry::has($array, 'names'));
        $this->assertTrue(Arry::has($array, 'names.developer'));
        $this->assertFalse(Arry::has($array, 'foo'));
        $this->assertFalse(Arry::has($array, 'foo.bar'));
    }

    public function testArraySet()
    {
        $array = [];
        Arry::set($array, 'names.developer', 'taylor');
        $this->assertEquals('taylor', $array['names']['developer']);
    }

    public function testArrayForget()
    {
        $array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle']];
        Arry::forget($array, 'names.developer');
        $this->assertFalse(isset($array['names']['developer']));
        $this->assertTrue(isset($array['names']['otherDeveloper']));

        $array = ['names' => ['developer' => 'taylor', 'otherDeveloper' => 'dayle', 'thirdDeveloper' => 'Lucas']];
        Arry::forget($array, ['names.developer', 'names.otherDeveloper']);
        $this->assertFalse(isset($array['names']['developer']));
        $this->assertFalse(isset($array['names']['otherDeveloper']));
        $this->assertTrue(isset($array['names']['thirdDeveloper']));

        $array = [
            'names'      => ['developer' => 'taylor', 'otherDeveloper' => 'dayle'],
            'otherNames' => ['developer' => 'Lucas', 'otherDeveloper' => 'Graham']
        ];
        Arry::forget($array, ['names.developer', 'otherNames.otherDeveloper']);
        $expected = ['names' => ['otherDeveloper' => 'dayle'], 'otherNames' => ['developer' => 'Lucas']];
        $this->assertEquals($expected, $array);
    }

    public function testArrayPluckWithArrayAndObjectValues()
    {
        $array = [(object)['name' => 'taylor', 'email' => 'foo'], ['name' => 'dayle', 'email' => 'bar']];
        $this->assertEquals(['taylor', 'dayle'], Arry::pluck($array, 'name'));
        $this->assertEquals(['taylor' => 'foo', 'dayle' => 'bar'], Arry::pluck($array, 'email', 'name'));
    }

    public function testArrayPluckWithNestedKeys()
    {
        $array = [['user' => ['taylor', 'otwell']], ['user' => ['dayle', 'rees']]];
        $this->assertEquals(['taylor', 'dayle'], Arry::pluck($array, 'user.0'));
        $this->assertEquals(['taylor', 'dayle'], Arry::pluck($array, ['user', 0]));
        $this->assertEquals(['taylor' => 'otwell', 'dayle' => 'rees'], Arry::pluck($array, 'user.1', 'user.0'));
        $this->assertEquals(['taylor' => 'otwell', 'dayle' => 'rees'], Arry::pluck($array, ['user', 1], ['user', 0]));
    }

    public function testArrayExcept()
    {
        $array = ['name' => 'taylor', 'age' => 26];
        $this->assertEquals(['age' => 26], Arry::except($array, ['name']));
        $this->assertEquals(['age' => 26], Arry::except($array, 'name'));

        $array = ['name' => 'taylor', 'framework' => ['language' => 'PHP', 'name' => 'Laravel']];
        $this->assertEquals(['name' => 'taylor'], Arry::except($array, 'framework'));
        $this->assertEquals(['name' => 'taylor', 'framework' => ['name' => 'Laravel']],
            Arry::except($array, 'framework.language'));
        $this->assertEquals(['framework' => ['language' => 'PHP']], Arry::except($array, ['name', 'framework.name']));
    }

    public function testArrayOnly()
    {
        $array = ['name' => 'taylor', 'age' => 26];
        $this->assertEquals(['name' => 'taylor'], Arry::only($array, ['name']));
        $this->assertSame([], Arry::only($array, ['nonExistingKey']));
    }

    public function testArrayCollapse()
    {
        $array = [[1], [2], [3], ['foo', 'bar'], ['baz', 'boom']];
        $this->assertEquals([1, 2, 3, 'foo', 'bar', 'baz', 'boom'], Arry::collapse($array));
    }

    public function testArrayDivide()
    {
        $array = ['name' => 'taylor'];
        list($keys, $values) = Arry::divide($array);
        $this->assertEquals(['name'], $keys);
        $this->assertEquals(['taylor'], $values);
    }

    public function testArrayFirst()
    {
        $array = ['name' => 'taylor', 'otherDeveloper' => 'dayle'];
        $this->assertEquals('dayle', Arry::first($array, function ($key, $value) {
            return $value == 'dayle';
        }));
    }

    public function testArrayLast()
    {
        $array = [100, 250, 290, 320, 500, 560, 670];
        $this->assertEquals(670, Arry::last($array, function ($key, $value) {
            return $value > 320;
        }));
    }

    public function testArrayPluck()
    {
        $data = [
            'post-1' => [
                'comments' => [
                    'tags' => [
                        '#foo',
                        '#bar',
                    ],
                ],
            ],
            'post-2' => [
                'comments' => [
                    'tags' => [
                        '#baz',
                    ],
                ],
            ],
        ];

        $this->assertEquals([
            0 => [
                'tags' => [
                    '#foo',
                    '#bar',
                ],
            ],
            1 => [
                'tags' => [
                    '#baz',
                ],
            ],
        ], Arry::pluck($data, 'comments'));

        $this->assertEquals([['#foo', '#bar'], ['#baz']], Arry::pluck($data, 'comments.tags'));
        $this->assertEquals([null, null], Arry::pluck($data, 'foo'));
        $this->assertEquals([null, null], Arry::pluck($data, 'foo.bar'));
    }

    public function testArrayFlatten()
    {
        $this->assertEquals(['#foo', '#bar', '#baz'], Arry::flatten([['#foo', '#bar'], ['#baz']]));
    }

    public function testArraySort()
    {
        $array = [
            ['name' => 'baz'],
            ['name' => 'foo'],
            ['name' => 'bar'],
        ];

        $this->assertEquals([
            ['name' => 'bar'],
            ['name' => 'baz'],
            ['name' => 'foo']
        ],
            Arry::values(Arry::sort($array, function ($v) {
                return $v['name'];
            })));
    }

    public function testArrayWhere()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8];
        $this->assertEquals(['b' => 2, 'd' => 4, 'f' => 6, 'h' => 8], Arry::where(
            $array,
            function ($key, $value) {
                return $value % 2 === 0;
            }
        ));
    }

    public function testArrayAdd()
    {
        $this->assertEquals(['surname' => 'Mövsümov'], Arry::add([], 'surname', 'Mövsümov'));
        $this->assertEquals(['developer' => ['name' => 'Ferid']], Arry::add([], 'developer.name', 'Ferid'));
    }

    public function testArrayPull()
    {
        $developer = ['firstname' => 'Ferid', 'surname' => 'Mövsümov'];
        $this->assertEquals('Mövsümov', Arry::pull($developer, 'surname'));
        $this->assertEquals(['firstname' => 'Ferid'], $developer);
    }
}
