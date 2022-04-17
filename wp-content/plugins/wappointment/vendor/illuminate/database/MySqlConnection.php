<?php

namespace WappoVendor\Illuminate\Database;

use PDO;
use WappoVendor\Illuminate\Database\Schema\MySqlBuilder;
use WappoVendor\Illuminate\Database\Query\Processors\MySqlProcessor;
use WappoVendor\Doctrine\DBAL\Driver\PDOMySql\Driver as DoctrineDriver;
use WappoVendor\Illuminate\Database\Query\Grammars\MySqlGrammar as QueryGrammar;
use WappoVendor\Illuminate\Database\Schema\Grammars\MySqlGrammar as SchemaGrammar;
class MySqlConnection extends \WappoVendor\Illuminate\Database\Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Query\Grammars\MySqlGrammar());
    }
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (\is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new \WappoVendor\Illuminate\Database\Schema\MySqlBuilder($this);
    }
    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Schema\Grammars\MySqlGrammar());
    }
    /**
     * Get the default post processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\MySqlProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new \WappoVendor\Illuminate\Database\Query\Processors\MySqlProcessor();
    }
    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOMySql\Driver
     */
    protected function getDoctrineDriver()
    {
        return new \WappoVendor\Doctrine\DBAL\Driver\PDOMySql\Driver();
    }
    /**
     * Bind values to their parameters in the given statement.
     *
     * @param  \PDOStatement $statement
     * @param  array  $bindings
     * @return void
     */
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(\is_string($key) ? $key : $key + 1, $value, \is_int($value) || \is_float($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
    }
}
