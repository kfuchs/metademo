<?php namespace k\Reposed\Tools;

use k\Reposed\TestCase;

class SorterTest extends TestCase {

	public function testBasicSetterAndGetters()
	{
		$o = new Sorter;

		$sortables = ['foo', 'bar', 'baz'];
		$o->setSortables($sortables);
		$this->assertEquals($o->getSortables(), $sortables);

		$params = ['foo' => 'asc', 'bar' => 'desc'];
		$o->setParams($params);
		$this->assertEquals($params, $o->getParams());
	}

	public function testProcessedSortables()
	{
		$o = new Sorter;

		$sortables = ['foo', 'f.bar', 'baz' => 'f.bad'];
		$processed = $o->setSortables($sortables)->processedSortables();
		$expected = ['foo' => 'foo', 'f.bar' => 'f.bar', 'baz' => 'f.bad'];

		$this->assertEquals($processed, $expected);
	}

	public function testQueryOrderedAppropriatelyWhenRun()
	{
		/*
		$o = new Sorter;

		$sortables = ['foo', 'f.bar', 'baz' => 'f.bad'];
		$o->setSortables($sortables);
		
		$params = ['foo' => 'asc', 'f.bar' => 'wrong', 'baz' => 'desc'];
		$o->setParams($params);

		$q->shouldReceive('orderBy')->once()->with('t.foo', 'asc');
		$q->shouldReceive('orderBy')->once()->with('f.bad', 'desc');

		$o->run();
		*/
	}

}