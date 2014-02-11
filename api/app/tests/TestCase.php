<?php

use Mockery as m;
use Command\Db\Reseed;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	protected $useDatabase = false;
	protected $filtersEnabled = true;

	/**
	 * Creates the application.
	 *
	 * @return Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	public function setUp()
	{
		parent::setUp();
		if($this->useDatabase)
		{
			$this->setUpDb();
		}
		
		if($this->filtersEnabled)
		{
			Route::enableFilters();
		}
	}

	public function teardown()
	{
		m::close();
	}

	public function setUpDb()
	{
		(new Reseed)->fire();
	}

}