<?php namespace k\Reposed;

use \Mockery as m;

class RepositoryFactoryTest extends TestCase {

	public function testFactoryCreatesProperRepositoryWhenSuppliedModelClass()
	{
		$f = new RepositoryFactory;
		$repo = $f->of('k\Reposed\DefaultRepoModelStub');

		$this->assertInstanceOf('k\Reposed\Repository', $repo);
		$this->assertInstanceOf('k\Reposed\DefaultRepoModelStub', $repo->getModel());
	}

	public function testFactoryCreatesProperRepositoryWhenSuppliedModelInstance()
	{
		$f = new RepositoryFactory;
		$repo = $f->of(new DefaultRepoModelStub);

		$this->assertInstanceOf('k\Reposed\Repository', $repo);
		$this->assertInstanceOf('k\Reposed\DefaultRepoModelStub', $repo->getModel());
	}

	public function testFactoryCreatesProperRepositoryForModelWithDifferentRepository()
	{
		$f = new RepositoryFactory;
		$repo = $f->of('k\Reposed\DifferentRepoModelStub');

		$this->assertInstanceOf('k\Reposed\DifferentRepoStub', $repo);
		$this->assertInstanceOf('k\Reposed\DifferentRepoModelStub', $repo->getModel());
	}

}


class DefaultRepoModelStub extends Model { }

class DifferentRepoStub extends Repository { }

class DifferentRepoModelStub extends Model {

	public function newRepository()
	{
		return new DifferentRepoStub($this);
	}

}