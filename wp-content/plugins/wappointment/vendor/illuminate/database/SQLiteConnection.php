<?php

namespace WappoVendor\Illuminate\Database;

use WappoVendor\Illuminate\Database\Schema\SQLiteBuilder;
use WappoVendor\Illuminate\Database\Query\Processors\SQLiteProcessor;
use WappoVendor\Doctrine\DBAL\Driver\PDOSqlite\Driver as DoctrineDriver;
use WappoVendor\Illuminate\Database\Query\Grammars\SQLiteGrammar as QueryGrammar;
use WappoVendor\Illuminate\Database\Schema\Grammars\SQLiteGrammar as SchemaGrammar;
class SQLiteConnection extends \WappoVendor\Illuminate\Database\Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\SQLiteGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Query\Grammars\SQLiteGrammar());
    }
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\SQLiteBuilder
     */
    public function getSchemaBuilder()
    {
        if (\is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new \WappoVendor\Illuminate\Database\Schema\SQLiteBuilder($this);
    }
    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\SQLiteGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Schema\Grammars\SQLiteGrammar());
    }
    /**
     * Get the default post processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\SQLiteProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new \WappoVendor\Illuminate\Database\Query\Processors\SQLiteProcessor();
    }
    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOSqlite\Driver
     */
    protected function getDoctrineDriver()
    {
        return new \WappoVendor\Doctrine\DBAL\Driver\PDOSqlite\Driver();
    }
}
