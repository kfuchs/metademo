<?php namespace k\Reposed\Tools;

use k\Reposed\TestCase;
use k\Reposed\Repository;

class FiltererTest extends TestCase {

	public function setup()
	{
		date_default_timezone_set('Asia/Kolkata');
	}

	public function testBasicSettersAndGetters()
	{
		$f = new Filterer;

		$dates = ['foo', 'bar'];
		$f->setDates($dates);
		$this->assertEquals($f->getDates(), $dates);

		$extraDates = ['baz', 'bad'];
		$f->appendDates($extraDates);
		$this->assertEquals($f->getDates(), array_merge($dates, $extraDates));

		$filterables = ['foo' => 'bar', 'baz' => 'bad', 'fizz' => 'buzz'];
		$f->setFilterables($filterables);
		$this->assertEquals($f->getFilterables(), $filterables);

		$filter = function ($q, $f, $p) { return true; };
		$f->setFilter('foo', $filter);
		$this->assertEquals($f->getFilter('foo'), $filter);

		$params = ['foo' => 'bar'];
		$f->setParams($params);
		$this->assertEquals($f->getParams(), $params);

		$processor = function ($k, $v) { return $v; };
		$f->setParamProcessor('foo', $processor);
		$this->assertEquals($processor, $f->getParamProcessor('foo'));
	}

	public function testProcessedFilterables()
	{
		$f = new Filterer;
		$f->setFilterables(['foo', 'bar', 'baz' => 't.baz', 'b.foobar']);

		$processed = $f->processedFilterables();

		$this->assertEquals($processed, ['foo' => 'foo', 'bar' => 'bar', 'baz' => 't.baz', 'b.foobar' => 'b.foobar']);
	}

	public function testProcessedParams()
	{
		$f = new Filterer();
		$filterables = ['foo' => 'f.foo', 'bar', 'baz', 'bad'];
		$dates = ['baz', 'bad'];
		$params = ['foo' => 'foo', 'bar' => ['20', '40'], 'baz' => '9-May-2013', 'bad' => ['5-May-2013', '13-May-2013']];

		$f->setFilterables($filterables)->setDates($dates)->setParams($params);

		$f->setParamProcessor('bar', function ($field, $param) {
			return array_map(function ($el) { return $el * 2; }, $param);
		});

		$processed = $f->processedParams();

		$dtMaker = function ($s) {
			$dt = new \DateTime;
			$dt->setTimestamp(strtotime($s));
			return $dt;
		};

		$expected = [
			'foo' => 'foo', 'bar' => ['40', '80'],
			'baz' => $dtMaker('9-May-2013'),
			'bad' => [$dtMaker('5-May-2013'), $dtMaker('13-May-2013')]
		];

		$this->assertEquals($processed, $expected);
	}

	public function testQueryFilteringAppropriatelyWhenRun()
	{
		$q = $this->mockBuilder();
		$m = $this->mockModel();
		$q->expects($this->any())->method('getModel')->will($this->returnValue($m));
		$m->expects($this->any())->method('getTable')->will($this->returnValue('t'));

		$r = new Repository($m);

		$f = new Filterer($q);
		$filterables = ['foo' => 'f.foo', 'bar', 'baz', 'bad'];
		$dates = ['baz', 'bad'];
		$params = ['foo' => 'foo', 'bar' => ['20', '40'], 'baz' => '9-May-2013', 'bad' => ['5-May-2013', '13-May-2013']];

		$f->setFilterables($filterables)->setDates($dates)->setParams($params);

		$dtMaker = function ($s) {
			$dt = new \DateTime;
			$dt->setTimestamp(strtotime($s));
			return $dt;
		};

		$q->expects($this->once())->method('where')->with('f.foo', 'like', '%foo%')->will($this->returnSelf());
		$q->expects($this->once())->method('where')->with('t.bar', '>=', '20')->will($this->returnSelf());
		$q->expects($this->once())->method('where')->with('t.bar', '<=', '40')->will($this->returnSelf());
		$q->expects($this->once())->method('where')->with('baz', 'like', $dtMaker($params['baz']))->will($this->returnSelf());
		$q->expects($this->once())->method('where')->with('bad', '>=', $dtMaker($params['bad'][0]))->will($this->returnSelf());
		$q->expects($this->once())->method('where')->with('bad', '<=', $dtMaker($params['bad'][1]))->will($this->returnSelf());

		$f->run($r);

		$r->query();
	}

}