<?php namespace k\Reposed\Tools;

use k\Reposed\TestCase;

class PersistorTest extends TestCase {

	public function setup()
	{
		date_default_timezone_set('Asia/Kolkata');
	}

	public function testBasicSettersAndGetters()
	{
		$p = new Persistor();

		$m = $this->mockModel();
		$p->setModel($m);
		$this->assertEquals($p->getModel(), $m);

		$fields = ['foo', 'bar', 'baz']; $rels = ['foobar', 'foobaz'];
		$p->setFields($fields)->setRelations($rels);
		$this->assertEquals($p->getFields(), $fields);
		$this->assertEquals($p->getRelations(), $rels);		
	}

	public function testPersistorSetsCorrectDateTimesOnDataProcessing()
	{
		$m = $this->mockModel();
		$p = new Persistor($m);

		$m->expects($this->any())->method('getDates')->will($this->returnValue(['bar', 'baz']));

		$p->setFields(['foo', 'bar', 'baz']);

		// since the internal converter uses strtotime, we'd test two different formats accepted by strtotime
		// 10-May-2013 
		// next month
		
		$data = ['foo' => '10/05/2013', 'bar'=>'10-May-2013', 'baz' => 'next month'];
		$p->setData($data);
		$processed = $p->processedData();

		$this->assertInternalType('string', $processed['foo']);
		$this->assertEquals($processed['foo'], $data['foo']);

		$this->assertInstanceOf('DateTime', $processed['bar']);
		$this->assertInstanceOf('DateTime', $processed['baz']);
	}

	public function testPersistorSavesAllFieldsWhenCreating()
	{
		$m = $this->mockModel();
		$p = new Persistor($m);
		$m->exists = false;
		$m->expects($this->any())->method('getDates')->will($this->returnValue([]));
		$p->setFields(['foo', 'bar', 'baz', 'foobar']);
		$data = ['foo' => '10/05/2013', 'bar'=>'10-May-2013', 'baz' => 'next month'];
		$processed = $p->setData($data)->processedData();

		$m->expects($this->once())->method('setAttribute')->with('foo', $processed['foo']);
		$m->expects($this->once())->method('setAttribute')->with('bar', $processed['bar']);
		$m->expects($this->once())->method('setAttribute')->with('baz', $processed['baz']);
		$m->expects($this->once())->method('setAttribute')->with('foobar', null);
		$m->expects($this->once())->method('save');
		$p->run();
	}

	public function testPersistorSavesProvidedFieldsInUpdateMode()
	{
		$m = $this->mockModel();
		$p = new Persistor($m);
		$m->exists = true;
		$m->expects($this->any())->method('getDates')->will($this->returnValue([]));
		$p->setFields(['foo', 'bar', 'baz', 'foobar']);
		$data = ['foo' => '10/05/2013', 'bar'=>'10-May-2013', 'baz' => 'next month'];
		$processed = $p->setData($data)->processedData();

		$m->expects($this->once())->method('setAttribute')->with('foo', $processed['foo']);
		$m->expects($this->once())->method('setAttribute')->with('bar', $processed['bar']);
		$m->expects($this->once())->method('setAttribute')->with('baz', $processed['baz']);
		$m->expects($this->once())->method('save');
		$p->run();	
	}

}