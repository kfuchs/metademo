<?php namespace k\Reposed\Database\Connection;

use k\Reposed\Database\Connection;
use Illuminate\Database\Schema;
use Illuminate\Database\Query;

class SQLite extends Connection {

    protected $doctrineDriver;

    protected function newDoctrineDriver()
    {
        return new \Doctrine\DBAL\Driver\PDOSqlite\Driver;
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
        return $this->withTablePrefix(new Query\Grammars\SQLiteGrammar);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new Schema\Grammars\SQLiteGrammar);
    }

}