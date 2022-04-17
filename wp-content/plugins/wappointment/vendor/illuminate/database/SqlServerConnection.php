<?php

namespace WappoVendor\Illuminate\Database;

use Closure;
use Exception;
use Throwable;
use WappoVendor\Illuminate\Database\Schema\SqlServerBuilder;
use WappoVendor\Doctrine\DBAL\Driver\PDOSqlsrv\Driver as DoctrineDriver;
use WappoVendor\Illuminate\Database\Query\Processors\SqlServerProcessor;
use WappoVendor\Illuminate\Database\Query\Grammars\SqlServerGrammar as QueryGrammar;
use WappoVendor\Illuminate\Database\Schema\Grammars\SqlServerGrammar as SchemaGrammar;
class SqlServerConnection extends \WappoVendor\Illuminate\Database\Connection
{
    /**
     * Execute a Closure within a transaction.
     *
     * @param  \Closure  $callback
     * @param  int  $attempts
     * @return mixed
     *
     * @throws \Exception|\Throwable
     */
    public function transaction(\Closure $callback, $attempts = 1)
    {
        for ($a = 1; $a <= $attempts; $a++) {
            if ($this->getDriverName() == 'sqlsrv') {
                return parent::transaction($callback);
            }
            $this->getPdo()->exec('BEGIN TRAN');
            // We'll simply execute the given callback within a try / catch block
            // and if we catch any exception we can rollback the transaction
            // so that none of the changes are persisted to the database.
            try {
                $result = $callback($this);
                $this->getPdo()->exec('COMMIT TRAN');
            } catch (\Exception $e) {
                $this->getPdo()->exec('ROLLBACK TRAN');
                throw $e;
            } catch (\Throwable $e) {
                $this->getPdo()->exec('ROLLBACK TRAN');
                throw $e;
            }
            return $result;
        }
    }
    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\SqlServerGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Query\Grammars\SqlServerGrammar());
    }
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\SqlServerBuilder
     */
    public function getSchemaBuilder()
    {
        if (\is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
        return new \WappoVendor\Illuminate\Database\Schema\SqlServerBuilder($this);
    }
    /**
     * Get the default schema grammar instance.
     *
     * @return \Illuminate\Database\Schema\Grammars\SqlServerGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new \WappoVendor\Illuminate\Database\Schema\Grammars\SqlServerGrammar());
    }
    /**
     * Get the default post processor instance.
     *
     * @return \Illuminate\Database\Query\Processors\SqlServerProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new \WappoVendor\Illuminate\Database\Query\Processors\SqlServerProcessor();
    }
    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOSqlsrv\Driver
     */
    protected function getDoctrineDriver()
    {
        return new \WappoVendor\Doctrine\DBAL\Driver\PDOSqlsrv\Driver();
    }
}
