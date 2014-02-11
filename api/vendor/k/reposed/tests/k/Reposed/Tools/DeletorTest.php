<?php namespace k\Reposed\Tools;

use k\Reposed\TestCase;
use Illuminate\Support\MessageBag;

class DeletorTest extends TestCase {

	public function testBasicSetterAndGetters()
	{
		$d = new Deletor;

		$m = $this->mockModel();
		$d->setModel($m);
		$this->assertEquals($m, $d->getModel());

		$deletor = function () { return 'foobar'; };
		$d->setDeletor($deletor);
		$this->assertEquals($deletor, $d->getDeletor());

		$preReqs = function () { return 'foobar'; };
		$d->setPreReqs($preReqs);
		$this->assertEquals($preReqs, $d->getPreReqs());

		$deletionTerms = new MessageBag(['foo' => 'bar']);
		$d->setDeletionTerms($deletionTerms);
		$this->assertEquals($deletionTerms, $d->getDeletionTerms());
	}

	public function testDefaultDeletionOperation()
	{
		$m = $this->mockModel();
		$d = new Deletor($m);

		$m->expects($this->once())->method('delete');

		$d->run();
	}

	public function testCustomDeletionOperation()
	{
		$m = $this->mockModel();
		$d = new Deletor($m);

		$d->setDeletor(function ($m) { $m->foobar('foo', 'bar'); $m->delete(); });
		$d->setPreReqs(function ($m) { return $m->check(); });

		$m->expects($this->once())->method('check')->will($this->returnValue(true));
		$m->expects($this->once())->method('foobar');
		$m->expects($this->once())->method('delete');

		$d->run();
	}

}