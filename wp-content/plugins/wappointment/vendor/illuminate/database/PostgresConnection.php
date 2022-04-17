<?php

namespace WappoVendor\Illuminate\Database;

use WappoVendor\Illuminate\Database\Schema\PostgresBuilder;
use WappoVendor\Doctrine\DBAL\Driver\PDOPgSql\Driver as DoctrineDriver;
use WappoVendor\Illuminate\Database\Query\Processors\PostgresProcessor;
use WappoVendor\Illuminate\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use WappoVendor\Illuminate\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;
class PostgresConnection extends \WappoVendor\Illuminate\Database\Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\PostgresGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Query\Grammars\PostgresGrammar());
    }
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\PostgresBuilder
     */
    public function getSchemaBuilder()
    {
        if (\is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new \WappoVendor\Illuminate\Database\Schema\PostgresBuilder($this);
    }
    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\PostgresGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Schema\Grammars\PostgresGrammar());
    }
    /**
     * Get the default post processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\PostgresProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new \WappoVendor\Illuminate\Database\Query\Processors\PostgresProcessor();
    }
    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOPgSql\Driver
     */
    protected function getDoctrineDriver()
    {
        return new \WappoVendor\Doctrine\DBAL\Driver\PDOPgSql\Driver();
    }
}
