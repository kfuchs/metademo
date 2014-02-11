<?php namespace k\Reposed\Database;

use Illuminate\Database\Connection as IlluminateConnection;

class Connection extends IlluminateConnection {

	protected $doctrineSchemaManager;

	protected $doctrineConnection;

	/**
	 * Get the Doctrine DBAL schema manager for the connection.
	 *
	 * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	public function getDoctrineSchemaManager()
	{
		if($this->doctrineSchemaManager) return $this->doctrineSchemaManager;

		return $this->doctrineSchemaManager = $this->newDoctrineSchemaManager();
	}

	protected function newDoctrineSchemaManager()
	{
		return $this->getDoctrineDriver()->getSchemaManager($this->getDoctrineConnection());
	}

	/**
	 * Get the Doctrine DBAL database connection instance.
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getDoctrineConnection()
	{
		if($this->doctrineConnection) return $this->doctrineConnection;

		return $this->doctrineConnection = $this->newDoctrineConnection();
	}

	protected function newDoctrineConnection()
	{
		$driver = $this->getDoctrineDriver();

		$data = array('pdo' => $this->pdo, 'dbname' => $this->getConfig('database'));

		return new \Doctrine\DBAL\Connection($data, $driver);
	}

	/**
	 * Begin a fluent query against a database table.
	 *
	 * @param  string  $table
	 * @return \k\Reposed\Database\Query\Builder
	 */
	public function table($table)
	{
		$processor = $this->getPostProcessor();

		$query = new QueryBuilder($this, $this->getQueryGrammar(), $processor);

		return $query->from($table);
	}

}