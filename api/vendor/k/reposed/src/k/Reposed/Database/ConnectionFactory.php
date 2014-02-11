<?php namespace k\Reposed\Database;

use PDO;
use Illuminate\Container\Container;
use k\Reposed\Database\Connection\MySql as MysqlConnection;
use k\Reposed\Database\Connection\Postgres as PostgresConnection;
use k\Reposed\Database\Connection\SQLite as SQLiteConnection;
use k\Reposed\Database\Connection\SqlServer as SqlServerConnection;
use Illuminate\Database\Connectors\ConnectionFactory as IlluminateConnectionFactory;

class ConnectionFactory extends IlluminateConnectionFactory {

	/**
	 * Create a new connection instance.
	 *
	 * @param  string  $driver
	 * @param  PDO     $connection
	 * @param  string  $database
	 * @param  string  $prefix
	 * @param  array   $config
	 * @return \k\Reposed\Database\Connection
	 */
	protected function createConnection($driver, PDO $connection, $database, $prefix = '', $config = null)
	{
		if ($this->container->bound($key = "db.connection.{$driver}"))
		{
			return $this->container->make($key, array($connection, $database, $prefix, $config));
		}

		switch ($driver)
		{
			case 'mysql':
				return new MySqlConnection($connection, $database, $prefix, $config);

			case 'pgsql':
				return new PostgresConnection($connection, $database, $prefix, $config);

			case 'sqlite':
				return new SQLiteConnection($connection, $database, $prefix, $config);

			case 'sqlsrv':
				return new SqlServerConnection($connection, $database, $prefix, $config);
		}

		throw new \InvalidArgumentException("Unsupported driver [$driver]");
	}

}