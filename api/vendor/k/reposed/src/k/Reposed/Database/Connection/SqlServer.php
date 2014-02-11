<?php namespace k\Reposed\Database\Connection;

use k\Reposed\Database\Connection;
use Illuminate\Database\Schema;
use Illuminate\Database\Query;

class SqlServer extends Connection {

	protected $doctrineDriver;

    protected function newDoctrineDriver()
    {
        return new \Doctrine\DBAL\Driver\PDOSqlsrv\Driver;
    }

    protected function getDoctrineDriver()
    {
        if ($this->doctrineDriver) return $this->doctrineDriver;

        return $this->doctrineDriver = $this->newDoctrineDriver();
    }

    /**
     * Execute a Closure within a transaction.
     *
     * @param  Closure  $callback
     * @return mixed
     */
    public function transaction(Closure $callback)
    {
        $this->pdo->exec('BEGIN TRAN');

        // We'll simply execute the given callback within a try / catch block
        // and if we catch any exception we can rollback the transaction
        // so that none of the changes are persisted to the database.
        try
        {
            $result = $callback($this);

            $this->pdo->exec('COMMIT TRAN');
        }

        // If we catch an exception, we will roll back so nothing gets messed
        // up in the database. Then we'll re-throw the exception so it can
        // be handled how the developer sees fit for their applications.
        catch (\Exception $e)
        {
            $this->pdo->exec('ROLLBACK TRAN');

            throw $e;
        }

        return $result;
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\Grammars\Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new Query\Grammars\SqlServerGrammar);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new Schema\Grammars\SqlServerGrammar);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\Processor
     */
    protected function getDefaultPostProcessor()
    {
        return new Query\Processors\SqlServerProcessor;
    }

}