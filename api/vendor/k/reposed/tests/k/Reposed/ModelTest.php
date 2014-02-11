<?php namespace k\Reposed;


class ModelTest extends TestCase {

	public function testNewRepositoryMethodReturnsRepositoryInstance()
	{
		$m = new BaseModelStub;

		$this->assertInstanceOf('k\Reposed\Repository', $m->newRepository());
	}

	public function testNewExistingMethodGivesAnExistingInstance()
	{
		$m = new BaseModelStub;

		$this->assertTrue($m->newExisting()->exists);
	}

	public function testNewFromBuilderInstantiatesObjectsOfAppropriateClasses()
	{
		$m = new BaseModelStub;

		$fooAttrs = array('name' => 'fooAttrs', 'type' => 'foo');
		$barAttrs = array('name' => 'barAttrs', 'type' => 'bar');
		$defAttrs = array('name' => 'defAttrs', 'type' => 'default');

		$this->assertInstanceOf('k\Reposed\FooModelStub', $m->newFromBuilder($fooAttrs));
		$this->assertInstanceOf('k\Reposed\BarModelStub', $m->newFromBuilder($barAttrs));
		$this->assertInstanceOf('k\Reposed\BaseModelStub', $m->newFromBuilder($defAttrs));
	}

}

class BaseModelStub extends Model {

	protected static $unguarded = true;

	public function mapData(array $attributes)
	{
		$type = isset($attributes['type']) ? $attributes['type'] : null;

		switch ($type) {
			case 'foo': return new FooModelStub;
			case 'bar': return new BarModelStub;
			default: return new static;
		}
	}

}

class FooModelStub extends BaseModelStub { }
class BarModelStub extends BaseModelStub { }