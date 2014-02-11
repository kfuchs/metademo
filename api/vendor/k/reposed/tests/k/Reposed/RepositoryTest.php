<?php namespace k\Reposed;

class RepositoryTest extends TestCase {

	public function testRepositoryConstruction()
	{
		$m = $this->mockModel();
		$q = $this->mockBuilder();
		$m->expects($this->any())->method('newQuery')->will($this->returnValue($q));

		$r = new Repository($m);

		$this->assertInstanceOf(Repository::class, $r);
		$this->assertEquals($m, $r->getModel());
	}

	public function testRepoProvidesCorrectQuery()
	{
		$m = $this->mockModel();
		$q = $this->mockBuilder();
		$m->expects($this->any())->method('newQuery')->will($this->returnValue($q));
		$q->expects($this->any())->method('getModel')->will($this->returnValue($m));

		$r = new Repository($m);

		$this->assertEquals($r->query(), $q);
		$this->assertEquals($r->query()->getModel(), $m);
	}

	public function testRepositoryScoping()
	{
		$m = $this->mockModel();
		$q = $this->mockBuilder();
		$m->expects($this->any())->method('newQuery')->will($this->returnValue($q));
		$q->expects($this->any())->method('getModel')->will($this->returnValue($m));
		$q->expects($this->any())->method('where')->will($this->returnSelf());

		$r = new Repository($m);

		$r->addScope(function ($q) {
			$q->where('foo', '=', 'bar');
		});

		$this->assertEquals($r->query(), $q);
	}

	public function testRepositoryScopeChaining()
	{
		$m = $this->mockModel();
		$q = $this->mockBuilder();
		$m->expects($this->any())->method('newQuery')->will($this->returnValue($q));
		$q->expects($this->any())->method('getModel')->will($this->returnValue($m));
		$q->expects($this->any())->method('where')->with('foo', '=', 'bar')->will($this->returnSelf());
		$m->expects($this->any())->method('newInstance')->will($this->returnValue($m));
		$m->expects($this->any())->method('getAllowedExtraFields')->will($this->returnValue([]));

		$testParam = 'foobar';

		$q->expects($this->any())->method('orWhere')->with('baz', '=', $testParam)->will($this->returnSelf());

		$r = new ChainingRepoStub($m);

		$r->addScope(function ($q) {
			$q->where('foo', '=', 'bar');
		});

		$chained = $r->forFooBar($testParam);

		$this->assertEquals($chained->query(), $q);
	}

	public function testRepositoryOffloadsMethodCallsToNewModelInstance()
	{
		$m = new OffloadModelStub;

		$r = new Repository($m);

		$this->assertEquals($r->foobar(), 'success');
	}

	public function testRepositoryImplementsEachJointOnlyOnce()
	{
		$m = $this->mockModel();
		$q = $this->mockBuilder();
		$m->expects($this->any())->method('newInstance')->will($this->returnValue($m));
		$m->expects($this->any())->method('newQuery')->will($this->returnValue($q));
		$m->expects($this->any())->method('newInstance')->will($this->returnValue($m));
		$m->expects($this->any())->method('getAllowedExtraFields')->will($this->returnValue([]));

		$r = new JointRepoStub($m);

		$r = $r->barFooBarFoo();

		$this->assertSame(['joinBar', 'joinFoo'], $r->getJoints());
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testJointingMethodsThrowExceptionIfCalledWithArgs()
	{
		$m = $this->mockModel();
		$q = $this->mockBuilder();
		$m->expects($this->any())->method('newInstance')->will($this->returnValue($m));
		$m->expects($this->any())->method('newQuery')->will($this->returnValue($q));

		$q->expects($this->any())->method('join')->will($this->returnValue($q));

		$r = new JointExceptionRepoStub($m);

		$r->foo();
	}

}

class OffloadModelStub extends Model {

	public function foobar()
	{
		return 'success';
	}

}

class JointExceptionRepoStub extends Repository {

	protected function joinFoo($table = null)
	{
		return $this->newJoint(function ($q) {
			$q->join('foo', 'foo.id', '=', 'this.foo_id');
		});
	}

	public function foo()
	{
		return $this->joinFoo('anything');
	}

}

class JointRepoStub extends Repository {

	protected function joinFoo()
	{
		return $this->newJoint(function ($q) {
			$q->join('foo', 'foo.id', '=', 'this.foo_id');
		});
	}

	protected function joinBar()
	{
		return $this->newJoint(function ($q) {
			$q->join('bar', 'bar.id', '=', 'this.bar_id');
		});
	}

	public function fooBar()
	{
		return $this->joinFoo()->joinBar();
	}

	public function fooBarFoo()
	{
		return $this->joinFoo()->joinBar()->joinFoo();
	}

	public function barFooBarFoo()
	{
		return $this->joinBar()->fooBarFoo();
	}

}

class ChainingRepoStub extends Repository {

	public function forFooBar($foo)
	{
		return $this->newScoped(function ($q) use($foo) {
			$q->orWhere('baz', '=', $foo);
		});
	}	

}
