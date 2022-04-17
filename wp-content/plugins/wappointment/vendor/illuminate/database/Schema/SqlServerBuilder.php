<?php

namespace WappoVendor\Illuminate\Database\Schema;

class SqlServerBuilder extends \WappoVendor\Illuminate\Database\Schema\Builder
{
    /**
     * Drop all tables from the database.
     *
     * @return void
     */
    public function dropAllTables()
    {
        $this->disableForeignKeyConstraints();
        $this->connection->statement($this->grammar->compileDropAllTables());
        $this->enableForeignKeyConstraints();
    }
}
