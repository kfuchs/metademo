<?php namespace k\Reposed;

class SnakeModelTest extends TestCase {

	public function testSnakePropertiesCanBeRetrievedViaCamelAccess()
	{
		$m = new SnakeModelStub(['foo_bar' => 1, 'baz_bad_bar' => 2]);

		$this->assertEquals($m->fooBar, 1);
		$this->assertEquals($m->bazBadBar, 2);
	}

	public function testPropertiesSetAsCamelCaseAreStoredInSnakeCase()
	{
		$m = new SnakeModelStub;
		$m->fooBarBaz = 'foo-bar-baz';
		$attrs = $m->getAttributes();

		$this->assertArrayHasKey('foo_bar_baz', $attrs);
		$this->assertEquals($m->fooBarBaz, $attrs['foo_bar_baz']);
		$this->assertEquals($attrs['foo_bar_baz'], 'foo-bar-baz');
	}

	public function testRelationsSetInCamelIsStoredInSnakeCase()
	{
		$m = new SnakeModelStub;
		$rel = new SnakeModelStub;

		$m->setRelation('fooBar', $rel);

		$this->assertEquals($m->fooBar, $rel);
		$this->assertEquals($m->getRelation('foo_bar'), $rel);
	}

}


class SnakeModelStub extends SnakeModel {

	protected static $unguarded = true;

}