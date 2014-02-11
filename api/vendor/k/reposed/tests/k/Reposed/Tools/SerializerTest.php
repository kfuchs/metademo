<?php namespace k\Reposed\Tools;

use k\Reposed\TestCase;
use k\Reposed\Model;
use k\Reposed\Collection;
use Illuminate\Support\Contracts\ArrayableInterface;

class SerializerTest extends TestCase {

	public function testSettingFieldsProcessesThemProperly()
	{
		$s = new Serializer;

		$s->setFields(['+foo', '-bar', '+foo.buzz', '-fizz.buzz', 'foo' => ['+bad', '-tree.free']]);

		$expected = [
			'+foo', '-bar',
			'foo' => ['+bad', '-tree.free', '+buzz'],
			'fizz' => '-buzz'
		];
		
		$this->assertEquals($expected, $s->getFields());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testIllegalKeyFieldsThrowException()
	{
		$s = new Serializer;

		$s->setFields(['+foo' => ['bar', 'bad']]);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testIllegalNormalFieldThrowsException()
	{
		$s = new Serializer;

		$s->setFields(['foo']);
	}

	public function testSimpleDataSerialization()
	{
		$s = new Serializer;

		$data = ['x' => 1, 'y' => 2, 'z' => 3];

		$this->assertEquals($data, $s->run($data));
	}

	public function testArraybleSerialization()
	{
		$s = new Serializer;
		
		$data = ['x' => 1, 'y' => 2, 'z' => 3];

		$arrayble = new ArrayableStub($data);

		$this->assertEquals($data, $s->run($arrayble));
	}



	public function testModelSerializtionWithoutAnyFields()
	{
		$s = new Serializer;
		$data = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'fizz' => 4, 'buzz' => 5];
		$m = new SerializableModelStub;

		foreach ($data as $key => $value) {
			$m->setAttribute($key, $value);
		}

		$this->assertEquals($data, $s->run($m));
	}

	public function testModelSerializationMinusSomeFields()
	{
		$s = new Serializer;
		$data = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'fizz' => 4, 'buzz' => 5];
		$m = new SerializableModelStub;

		foreach ($data as $key => $value) {
			$m->setAttribute($key, $value);
		}

		$this->assertEquals($data, $s->run($m));		
		$s->setFields(['-foo', '-fizz']);
		$expected = array_diff_key($data, ['foo' => null, 'fizz' => null]);
		$this->assertEquals($expected, $s->run($m));
	}

	public function testModelSerializationPlusSomeFields()
	{
		$s = new Serializer;
		$data = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'fizz' => 4, 'buzz' => 5, 'foobar' => 6, 'fizzbuzz' => 7];
		$m = new SerializableModelStub;

		foreach ($data as $key => $value) {
			$m->setAttribute($key, $value);
		}
		
		$this->assertEquals(array_diff_key($data, array_flip(['foobar', 'fizzbuzz'])), $s->run($m));
		$s->setFields(['+foobar', '+fizzbuzz']);
		$this->assertEquals($data, $s->run($m));
	}

	public function testModelSerializationWithArraybleProperties()
	{
		$s = new Serializer;
		$data = ['foo' => 1,'bar' => 2, 'baz' => new ArrayableStub(['x' => 1, 'y' => 2])];
		$m = new NestedSerializableModelStub;

		foreach ($data as $key => $value) {
			$m->setAttribute($key, $value);
		}

		$expected = ['baz' => ['x' => 1, 'y' => 2]] + $data;
		$this->assertEquals($expected, $s->run($m));
	}

	public function testModelSerializationWithModelRelations()
	{
		$s = new Serializer;
		$nested = new SerializableModelStub;
		$nestedData = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'fizz' => 4, 'buzz' => 5];
		foreach ($nestedData as $key => $value) {
			$nested->setAttribute($key, $value);
		}

		$m = new NestedSerializableModelStub;
		$data = ['foo' => 1, 'bar' => 2];
		foreach ($data as $key => $value) {
			$m->setAttribute($key, $value);
		}
		$m->setRelation('baz', $nested);

		$s->setFields(['-baz.fizz', '-baz.foo']);
		$expected = [
			'foo' => 1, 'bar' => 2,
			'baz' => [
				'bar' => 2, 'baz' => 3, 'buzz' => 5
			]
		];

		$this->assertEquals($expected, $s->run($m));
	}

	public function testCollectionSerializationFallsBackToModelSerialization()
	{
		$one = $this->mockModel();
		$two = $this->mockModel();
		$three =  $this->mockModel();

		$c = Collection::make([$one, $two, $three]);

		foreach([$one, $two, $three] as $m)
		{
			$m->expects($this->any())->method('getSerializables')->will($this->returnValue(['foo', 'bar', 'baz']));
			$m->expects($this->any())->method('getAttribute')->with('foo')->will($this->returnValue(1));
			$m->expects($this->any())->method('getAttribute')->with('bar')->will($this->returnValue(2));
			$m->expects($this->any())->method('getAttribute')->with('baz')->will($this->returnValue(3));
			$m->expects($this->any())->method('getSerializedFields')->will($this->returnValue([]));
		}

		$s = new Serializer;

		$expected = [
			['foo' => 1, 'bar' => 2, 'baz' => 3],
			['foo' => 1, 'bar' => 2, 'baz' => 3],
			['foo' => 1, 'bar' => 2, 'baz' => 3]
		];

		$this->assertEquals($expected, $s->run($c));
	}

	public function testModelSerializationWithCollectionRelation()
	{
		$one = $this->mockModel();
		$two = $this->mockModel();
		$three =  $this->mockModel();

		$c = Collection::make([$one, $two, $three]);

		foreach([$one, $two, $three] as $m)
		{
			$m->expects($this->any())->method('getSerializables')->will($this->returnValue(['foo', 'bar', 'baz']));
			$m->expects($this->any())->method('getAttribute')->with('foo')->will($this->returnValue(1));
			$m->expects($this->any())->method('getAttribute')->with('bar')->will($this->returnValue(2));
			$m->expects($this->any())->method('getAttribute')->with('baz')->will($this->returnValue(3));
			$m->expects($this->any())->method('getSerializedFields')->will($this->returnValue([]));
		}

		$m = $this->mockModel();
		$m->expects($this->any())->method('getSerializables')->will($this->returnValue(['fizz', 'buzz']));
		$m->expects($this->any())->method('getAttribute')->with('fizz')->will($this->returnValue('fizz'));
		$m->expects($this->any())->method('getAttribute')->with('buzz')->will($this->returnValue($c));
		$m->expects($this->any())->method('getSerializedFields')->will($this->returnValue([]));

		$expected = [
			'fizz' => 'fizz',
			'buzz' => [
				['foo' => 1, 'bar' => 2],
				['foo' => 1, 'bar' => 2],
				['foo' => 1, 'bar' => 2]
			]
		];

		$s = new Serializer;

		$this->assertEquals($expected, $s->setFields(['-buzz.baz'])->run($m));
		$this->assertEquals([$expected, $expected], $s->run(Collection::make([$m, $m])));
	}

	public function testModelSerializationCallsSerializerMethodWhenItShould()
	{
		$m = new FieldSerializerModelStub;
		$m->foo = 'fizz'; $m->bar = 'buzz';

		$s = new Serializer;

		$expected = [
			'foo' => 'fizz',
			'bar' => 'buzz_serialized'
		];

		$this->assertEquals($expected, $s->run($m));
	}

	public function testGetSerializablesGetsFieldsFromAttributesRelationsVisibleAndHiddenByDefault()
	{
		$m = new SerializablesLessStub;
		$m->foo = 'foo_attr'; $m->bar = 'bar_attr'; $m->baz = 'baz_attr';
		$m->setRelation('fizz', [1, 2, 3]); $m->setRelation('buzz', [4, 5, 6]);

		$this->assertEquals(['foo', 'bar', 'fizz'], $m->getDefaultSerializables());
		$this->assertEquals(['foo', 'bar', 'fizz'], $m->getSerializables());
		
		$s = new Serializer;
		$expected = ['foo' => 'foo_attr', 'bar' => 'bar_attr', 'fizz' => [1,2,3]];
		$this->assertEquals($expected, $s->run($expected));
	}

}

class SerializablesLessStub extends Model {

	protected $visible = ['foo', 'bar', 'fizz', 'buzz'];

	protected $hidden = ['buzz'];

}


class FieldSerializerModelStub extends Model {

	public function getSerializables()
	{
		return ['foo', 'bar'];
	}

	public function serializeBar()
	{
		return $this->bar . '_serialized';
	}

}

class SerializableModelStub extends Model {

	public function getSerializables()
	{
		return ['foo', 'bar', 'baz', 'fizz', 'buzz'];
	}

}

class NestedSerializableModelStub extends Model {

	public function getSerializables()
	{
		return ['foo', 'bar', 'baz'];
	}

}


class ArrayableStub implements ArrayableInterface {

	protected $data;

	public function __construct(array $data = array())
	{
		$this->data = $data;
	}

	public function toArray()
	{
		return $this->data;
	}

}