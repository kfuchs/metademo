<?php namespace k\Reposed;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase {

	public function setup()
	{

	}

	public function teardown()
	{

	}

	protected function mockModel()
	{
		return $this->getMock(Model::class);
	}

	protected function mockBuilder()
	{
		return $this->getMockBuilder(Builder::class)
					->disableOriginalConstructor()
					->getMock();
	}

}