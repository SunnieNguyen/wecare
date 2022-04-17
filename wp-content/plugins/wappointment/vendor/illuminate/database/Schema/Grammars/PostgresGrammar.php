<?php

namespace WappoVendor\Illuminate\Database\Schema\Grammars;

use RuntimeException;
use WappoVendor\Illuminate\Support\Fluent;
use WappoVendor\Illuminate\Database\Schema\Blueprint;
class PostgresGrammar extends \WappoVendor\Illuminate\Database\Schema\Grammars\Grammar
{
    /**
     * If this Grammar supports schema changes wrapped in a transaction.
     *
     * @var bool
     */
    protected $transactions = true;
    /**
     * The possible column modifiers.
     *
     * @var array
     */
    protected $modifiers = ['Increment', 'Nullable', 'Default'];
    /**
     * The columns available as serials.
     *
     * @var array
     */
    protected $serials = ['bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger'];
    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists()
    {
        return 'select * from information_schema.tables where table_schema = ? and table_name = ?';
    }
    /**
     * Compile the query to determine the list of columns.
     *
     * @return string
     */
    public function compileColumnListing()
    {
        return 'select column_name from information_schema.columns where table_schema = ? and table_name = ?';
    }
    /**
     * Compile a create table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileCreate(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return \sprintf('%s table %s (%s)', $blueprint->temporary ? 'create temporary' : 'create', $this->wrapTable($blueprint), \implode(', ', $this->getColumns($blueprint)));
    }
    /**
     * Compile a column addition command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileAdd(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return \sprintf('alter table %s %s', $this->wrapTable($blueprint), \implode(', ', $this->prefixArray('add column', $this->getColumns($blueprint))));
    }
    /**
     * Compile a primary key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compilePrimary(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $columns = $this->columnize($command->columns);
        return 'alter table ' . $this->wrapTable($blueprint) . " add primary key ({$columns})";
    }
    /**
     * Compile a unique key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileUnique(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return \sprintf('alter table %s add constraint %s unique (%s)', $this->wrapTable($blueprint), $this->wrap($command->index), $this->columnize($command->columns));
    }
    /**
     * Compile a plain index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileIndex(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return \sprintf('create index %s on %s%s (%s)', $this->wrap($command->index), $this->wrapTable($blueprint), $command->algorithm ? ' using ' . $command->algorithm : '', $this->columnize($command->columns));
    }
    /**
     * Compile a spatial index key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileSpatialIndex(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $command->algorithm = 'gist';
        return $this->compileIndex($blueprint, $command);
    }
    /**
     * Compile a foreign key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileForeign(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $sql = parent::compileForeign($blueprint, $command);
        if (!\is_null($command->deferrable)) {
            $sql .= $command->deferrable ? ' deferrable' : ' not deferrable';
        }
        if ($command->deferrable && !\is_null($command->initiallyImmediate)) {
            $sql .= $command->initiallyImmediate ? ' initially immediate' : ' initially deferred';
        }
        return $sql;
    }
    /**
     * Compile a drop table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDrop(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return 'drop table ' . $this->wrapTable($blueprint);
    }
    /**
     * Compile a drop table (if exists) command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropIfExists(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return 'drop table if exists ' . $this->wrapTable($blueprint);
    }
    /**
     * Compile the SQL needed to drop all tables.
     *
     * @param  string  $tables
     * @return string
     */
    public function compileDropAllTables($tables)
    {
        return 'drop table "' . \implode('","', $tables) . '" cascade';
    }
    /**
     * Compile the SQL needed to retrieve all table names.
     *
     * @param  string  $schema
     * @return string
     */
    public function compileGetAllTables($schema)
    {
        return "select tablename from pg_catalog.pg_tables where schemaname = '{$schema}'";
    }
    /**
     * Compile a drop column command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropColumn(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $columns = $this->prefixArray('drop column', $this->wrapArray($command->columns));
        return 'alter table ' . $this->wrapTable($blueprint) . ' ' . \implode(', ', $columns);
    }
    /**
     * Compile a drop primary key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropPrimary(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $index = $this->wrap("{$blueprint->getTable()}_pkey");
        return 'alter table ' . $this->wrapTable($blueprint) . " drop constraint {$index}";
    }
    /**
     * Compile a drop unique key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropUnique(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $index = $this->wrap($command->index);
        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }
    /**
     * Compile a drop index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropIndex(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return "drop index {$this->wrap($command->index)}";
    }
    /**
     * Compile a drop spatial index command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropSpatialIndex(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return $this->compileDropIndex($blueprint, $command);
    }
    /**
     * Compile a drop foreign key command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileDropForeign(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $index = $this->wrap($command->index);
        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
    }
    /**
     * Compile a rename table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileRename(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        $from = $this->wrapTable($blueprint);
        return "alter table {$from} rename to " . $this->wrapTable($command->to);
    }
    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints()
    {
        return 'SET CONSTRAINTS ALL IMMEDIATE;';
    }
    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints()
    {
        return 'SET CONSTRAINTS ALL DEFERRED;';
    }
    /**
     * Create the column definition for a char type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeChar(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "char({$column->length})";
    }
    /**
     * Create the column definition for a string type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeString(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "varchar({$column->length})";
    }
    /**
     * Create the column definition for a text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeText(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'text';
    }
    /**
     * Create the column definition for a medium text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumText(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'text';
    }
    /**
     * Create the column definition for a long text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLongText(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'text';
    }
    /**
     * Create the column definition for an integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->autoIncrement ? 'serial' : 'integer';
    }
    /**
     * Create the column definition for a big integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBigInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->autoIncrement ? 'bigserial' : 'bigint';
    }
    /**
     * Create the column definition for a medium integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->autoIncrement ? 'serial' : 'integer';
    }
    /**
     * Create the column definition for a tiny integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->autoIncrement ? 'smallserial' : 'smallint';
    }
    /**
     * Create the column definition for a small integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSmallInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->autoIncrement ? 'smallserial' : 'smallint';
    }
    /**
     * Create the column definition for a float type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeFloat(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->typeDouble($column);
    }
    /**
     * Create the column definition for a double type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDouble(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'double precision';
    }
    /**
     * Create the column definition for a real type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeReal(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'real';
    }
    /**
     * Create the column definition for a decimal type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDecimal(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "decimal({$column->total}, {$column->places})";
    }
    /**
     * Create the column definition for a boolean type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBoolean(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'boolean';
    }
    /**
     * Create the column definition for an enum type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeEnum(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        $allowed = \array_map(function ($a) {
            return "'{$a}'";
        }, $column->allowed);
        return "varchar(255) check (\"{$column->name}\" in (" . \implode(', ', $allowed) . '))';
    }
    /**
     * Create the column definition for a json type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJson(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'json';
    }
    /**
     * Create the column definition for a jsonb type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJsonb(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'jsonb';
    }
    /**
     * Create the column definition for a date type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDate(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'date';
    }
    /**
     * Create the column definition for a date-time type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDateTime(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "timestamp({$column->precision}) without time zone";
    }
    /**
     * Create the column definition for a date-time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDateTimeTz(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "timestamp({$column->precision}) with time zone";
    }
    /**
     * Create the column definition for a time type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTime(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "time({$column->precision}) without time zone";
    }
    /**
     * Create the column definition for a time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimeTz(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "time({$column->precision}) with time zone";
    }
    /**
     * Create the column definition for a timestamp type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestamp(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        $columnType = "timestamp({$column->precision}) without time zone";
        return $column->useCurrent ? "{$columnType} default CURRENT_TIMESTAMP" : $columnType;
    }
    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestampTz(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        $columnType = "timestamp({$column->precision}) with time zone";
        return $column->useCurrent ? "{$columnType} default CURRENT_TIMESTAMP" : $columnType;
    }
    /**
     * Create the column definition for a year type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeYear(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->typeInteger($column);
    }
    /**
     * Create the column definition for a binary type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBinary(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'bytea';
    }
    /**
     * Create the column definition for a uuid type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeUuid(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'uuid';
    }
    /**
     * Create the column definition for an IP address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeIpAddress(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'inet';
    }
    /**
     * Create the column definition for a MAC address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMacAddress(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'macaddr';
    }
    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @throws \RuntimeException
     */
    protected function typeGeometry(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        throw new \RuntimeException('The database driver in use does not support the Geometry spatial column type.');
    }
    /**
     * Create the column definition for a spatial Point type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typePoint(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('point');
    }
    /**
     * Create the column definition for a spatial LineString type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLineString(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('linestring');
    }
    /**
     * Create the column definition for a spatial Polygon type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typePolygon(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('polygon');
    }
    /**
     * Create the column definition for a spatial GeometryCollection type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeGeometryCollection(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('geometrycollection');
    }
    /**
     * Create the column definition for a spatial MultiPoint type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMultiPoint(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('multipoint');
    }
    /**
     * Create the column definition for a spatial MultiLineString type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeMultiLineString(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('multilinestring');
    }
    /**
     * Create the column definition for a spatial MultiPolygon type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMultiPolygon(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->formatPostGisType('multipolygon');
    }
    /**
     * Format the column definition for a PostGIS spatial type.
     *
     * @param  string  $type
     * @return string
     */
    private function formatPostGisType(string $type)
    {
        return "geography({$type}, 4326)";
    }
    /**
     * Get the SQL for a nullable column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyNullable(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->nullable ? ' null' : ' not null';
    }
    /**
     * Get the SQL for a default column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyDefault(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $column)
    {
        if (!\is_null($column->default)) {
            return ' default ' . $this->getDefaultValue($column->default);
        }
    }
    /**
     * Get the SQL for an auto-increment column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyIncrement(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $column)
    {
        if (\in_array($column->type, $this->serials) && $column->autoIncrement) {
            return ' primary key';
        }
    }
}
