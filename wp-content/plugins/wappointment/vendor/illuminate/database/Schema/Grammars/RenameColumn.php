<?php

namespace WappoVendor\Illuminate\Database\Schema\Grammars;

use WappoVendor\Illuminate\Support\Fluent;
use WappoVendor\Doctrine\DBAL\Schema\Column;
use WappoVendor\Doctrine\DBAL\Schema\TableDiff;
use WappoVendor\Illuminate\Database\Connection;
use WappoVendor\Illuminate\Database\Schema\Blueprint;
use WappoVendor\Doctrine\DBAL\Schema\AbstractSchemaManager as SchemaManager;
class RenameColumn
{
    /**
     * Compile a rename column command.
     *
     * @param  \Illuminate\Database\Schema\Grammars\Grammar  $grammar
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @param  \Illuminate\Database\Connection  $connection
     * @return array
     */
    public static function compile(\WappoVendor\Illuminate\Database\Schema\Grammars\Grammar $grammar, \WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command, \WappoVendor\Illuminate\Database\Connection $connection)
    {
        $column = $connection->getDoctrineColumn($grammar->getTablePrefix() . $blueprint->getTable(), $command->from);
        $schema = $connection->getDoctrineSchemaManager();
        return (array) $schema->getDatabasePlatform()->getAlterTableSQL(static::getRenamedDiff($grammar, $blueprint, $command, $column, $schema));
    }
    /**
     * Get a new column instance with the new column name.
     *
     * @param  \Illuminate\Database\Schema\Grammars\Grammar  $grammar
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @param  \Doctrine\DBAL\Schema\Column  $column
     * @param  \Doctrine\DBAL\Schema\AbstractSchemaManager  $schema
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected static function getRenamedDiff(\WappoVendor\Illuminate\Database\Schema\Grammars\Grammar $grammar, \WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command, \WappoVendor\Doctrine\DBAL\Schema\Column $column, \WappoVendor\Doctrine\DBAL\Schema\AbstractSchemaManager $schema)
    {
        return static::setRenamedColumns($grammar->getDoctrineTableDiff($blueprint, $schema), $command, $column);
    }
    /**
     * Set the renamed columns on the table diff.
     *
     * @param  \Doctrine\DBAL\Schema\TableDiff  $tableDiff
     * @param  \Illuminate\Support\Fluent  $command
     * @param  \Doctrine\DBAL\Schema\Column  $column
     * @return \Doctrine\DBAL\Schema\TableDiff
     */
    protected static function setRenamedColumns(\WappoVendor\Doctrine\DBAL\Schema\TableDiff $tableDiff, \WappoVendor\Illuminate\Support\Fluent $command, \WappoVendor\Doctrine\DBAL\Schema\Column $column)
    {
        $tableDiff->renamedColumns = [$command->from => new \WappoVendor\Doctrine\DBAL\Schema\Column($command->to, $column->getType(), $column->toArray())];
        return $tableDiff;
    }
}
