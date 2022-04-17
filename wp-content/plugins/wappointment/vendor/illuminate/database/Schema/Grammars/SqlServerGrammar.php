<?php

namespace WappoVendor\Illuminate\Database\Schema\Grammars;

use WappoVendor\Illuminate\Support\Fluent;
use WappoVendor\Illuminate\Database\Schema\Blueprint;
class SqlServerGrammar extends \WappoVendor\Illuminate\Database\Schema\Grammars\Grammar
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
    protected $modifiers = ['Increment', 'Collate', 'Nullable', 'Default'];
    /**
     * The columns available as serials.
     *
     * @var array
     */
    protected $serials = ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger'];
    /**
     * Compile the query to determine if a table exists.
     *
     * @return string
     */
    public function compileTableExists()
    {
        return "select * from sysobjects where type = 'U' and name = ?";
    }
    /**
     * Compile the query to determine the list of columns.
     *
     * @param  string  $table
     * @return string
     */
    public function compileColumnListing($table)
    {
        return "select col.name from sys.columns as col\n                join sys.objects as obj on col.object_id = obj.object_id\n                where obj.type = 'U' and obj.name = '{$table}'";
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
        $columns = \implode(', ', $this->getColumns($blueprint));
        return 'create table ' . $this->wrapTable($blueprint) . " ({$columns})";
    }
    /**
     * Compile a column addition table command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileAdd(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $command)
    {
        return \sprintf('alter table %s add %s', $this->wrapTable($blueprint), \implode(', ', $this->getColumns($blueprint)));
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
        return \sprintf('alter table %s add constraint %s primary key (%s)', $this->wrapTable($blueprint), $this->wrap($command->index), $this->columnize($command->columns));
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
        return \sprintf('create unique index %s on %s (%s)', $this->wrap($command->index), $this->wrapTable($blueprint), $this->columnize($command->columns));
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
        return \sprintf('create index %s on %s (%s)', $this->wrap($command->index), $this->wrapTable($blueprint), $this->columnize($command->columns));
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
        return \sprintf('create spatial index %s on %s (%s)', $this->wrap($command->index), $this->wrapTable($blueprint), $this->columnize($command->columns));
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
        return \sprintf('if exists (select * from INFORMATION_SCHEMA.TABLES where TABLE_NAME = %s) drop table %s', "'" . \str_replace("'", "''", $this->getTablePrefix() . $blueprint->getTable()) . "'", $this->wrapTable($blueprint));
    }
    /**
     * Compile the SQL needed to drop all tables.
     *
     * @return string
     */
    public function compileDropAllTables()
    {
        return "EXEC sp_msforeachtable 'DROP TABLE ?'";
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
        $columns = $this->wrapArray($command->columns);
        return 'alter table ' . $this->wrapTable($blueprint) . ' drop column ' . \implode(', ', $columns);
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
        $index = $this->wrap($command->index);
        return "alter table {$this->wrapTable($blueprint)} drop constraint {$index}";
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
        return "drop index {$index} on {$this->wrapTable($blueprint)}";
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
        $index = $this->wrap($command->index);
        return "drop index {$index} on {$this->wrapTable($blueprint)}";
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
        return "sp_rename {$from}, " . $this->wrapTable($command->to);
    }
    /**
     * Compile the command to enable foreign key constraints.
     *
     * @return string
     */
    public function compileEnableForeignKeyConstraints()
    {
        return 'EXEC sp_msforeachtable @command1="print \'?\'", @command2="ALTER TABLE ? WITH CHECK CHECK CONSTRAINT all";';
    }
    /**
     * Compile the command to disable foreign key constraints.
     *
     * @return string
     */
    public function compileDisableForeignKeyConstraints()
    {
        return 'EXEC sp_msforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT all";';
    }
    /**
     * Create the column definition for a char type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeChar(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "nchar({$column->length})";
    }
    /**
     * Create the column definition for a string type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeString(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return "nvarchar({$column->length})";
    }
    /**
     * Create the column definition for a text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeText(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(max)';
    }
    /**
     * Create the column definition for a medium text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumText(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(max)';
    }
    /**
     * Create the column definition for a long text type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeLongText(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(max)';
    }
    /**
     * Create the column definition for an integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'int';
    }
    /**
     * Create the column definition for a big integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeBigInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'bigint';
    }
    /**
     * Create the column definition for a medium integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMediumInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'int';
    }
    /**
     * Create the column definition for a tiny integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTinyInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'tinyint';
    }
    /**
     * Create the column definition for a small integer type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSmallInteger(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'smallint';
    }
    /**
     * Create the column definition for a float type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeFloat(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'float';
    }
    /**
     * Create the column definition for a double type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDouble(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'float';
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
        return 'bit';
    }
    /**
     * Create the column definition for an enum type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeEnum(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(255)';
    }
    /**
     * Create the column definition for a json type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJson(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(max)';
    }
    /**
     * Create the column definition for a jsonb type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeJsonb(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(max)';
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
        return $column->precision ? "datetime2({$column->precision})" : 'datetime';
    }
    /**
     * Create the column definition for a date-time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeDateTimeTz(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->precision ? "datetimeoffset({$column->precision})" : 'datetimeoffset';
    }
    /**
     * Create the column definition for a time type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTime(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $column->precision ? "time({$column->precision})" : 'time';
    }
    /**
     * Create the column definition for a time (with time zone) type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimeTz(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return $this->typeTime($column);
    }
    /**
     * Create the column definition for a timestamp type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestamp(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        $columnType = $column->precision ? "datetime2({$column->precision})" : 'datetime';
        return $column->useCurrent ? "{$columnType} default CURRENT_TIMESTAMP" : $columnType;
    }
    /**
     * Create the column definition for a timestamp (with time zone) type.
     *
     * @link https://msdn.microsoft.com/en-us/library/bb630289(v=sql.120).aspx
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeTimestampTz(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        if ($column->useCurrent) {
            $columnType = $column->precision ? "datetimeoffset({$column->precision})" : 'datetimeoffset';
            return "{$columnType} default CURRENT_TIMESTAMP";
        }
        return "datetimeoffset({$column->precision})";
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
        return 'varbinary(max)';
    }
    /**
     * Create the column definition for a uuid type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeUuid(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'uniqueidentifier';
    }
    /**
     * Create the column definition for an IP address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeIpAddress(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(45)';
    }
    /**
     * Create the column definition for a MAC address type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeMacAddress(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'nvarchar(17)';
    }
    /**
     * Create the column definition for a spatial Geometry type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeGeometry(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial Point type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typePoint(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial LineString type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeLineString(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial Polygon type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typePolygon(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial GeometryCollection type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeGeometryCollection(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial MultiPoint type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeMultiPoint(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial MultiLineString type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeMultiLineString(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Create the column definition for a spatial MultiPolygon type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    public function typeMultiPolygon(\WappoVendor\Illuminate\Support\Fluent $column)
    {
        return 'geography';
    }
    /**
     * Get the SQL for a collation column modifier.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $column
     * @return string|null
     */
    protected function modifyCollate(\WappoVendor\Illuminate\Database\Schema\Blueprint $blueprint, \WappoVendor\Illuminate\Support\Fluent $column)
    {
        if (!\is_null($column->collation)) {
            return ' collate ' . $column->collation;
        }
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
            return ' identity primary key';
        }
    }
    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $table
     * @return string
     */
    public function wrapTable($table)
    {
        if ($table instanceof \WappoVendor\Illuminate\Database\Schema\Blueprint && $table->temporary) {
            $this->setTablePrefix('#');
        }
        return parent::wrapTable($table);
    }
}