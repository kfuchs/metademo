<?php namespace k\Reposed\Database\Connection;

use k\Reposed\Database\Connection;
use Illuminate\Database\Schema;
use Illuminate\Database\Query;

class MySql extends Connection {

	protected $doctrineDriver;

	protected function newDoctrineDriver()
	{
		return new \Doctrine\DBAL\Driver\PDOMySql\Driver;
	}

	protected function getDoctrineDriver()
	{
		if ($this->doctrineDriver) return $this->doctrineDriver;

		return $this->doctrineDriver = $this->newDoctrineDriver();
	}

	/**
	 * Get the default query grammar instance.
	 *
	 * @return \Illuminate\Database\Query\Grammars\Grammars\Grammar
	 */
	protected function getDefaultQueryGrammar()
	{
		return $this->withTablePrefix(new Query\Grammars\MySqlGrammar);
	}

	/**
	 * Get the default schema grammar instance.
	 *
	 * @return \Illuminate\Database\Schema\Grammars\Grammar
	 */
	protected function getDefaultSchemaGrammar()
	{
		return $this->withTablePrefix(new Schema\Grammars\MySqlGrammar);
	}

	/**
	 * Get a schema builder instance for the connection.
	 *
	 * @return \Illuminate\Database\Schema\Builder
	 */
	public function getSchemaBuilder()
	{
		if (is_null($this->schemaGrammar)) { $this->useDefaultSchemaGrammar(); }

		return new Schema\MySqlBuilder($this);
	}

}