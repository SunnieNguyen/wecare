<?php

namespace WappoVendor\Illuminate\Database\Query\Processors;

use WappoVendor\Illuminate\Database\Query\Builder;
class PostgresProcessor extends \WappoVendor\Illuminate\Database\Query\Processors\Processor
{
    /**
     * Process an "insert get ID" query.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $sql
     * @param  array   $values
     * @param  string  $sequence
     * @return int
     */
    public function processInsertGetId(\WappoVendor\Illuminate\Database\Query\Builder $query, $sql, $values, $sequence = null)
    {
        $result = $query->getConnection()->selectFromWriteConnection($sql, $values)[0];
        $sequence = $sequence ?: 'id';
        $id = \is_object($result) ? $result->{$sequence} : $result[$sequence];
        return \is_numeric($id) ? (int) $id : $id;
    }
    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return \array_map(function ($result) {
            return ((object) $result)->column_name;
        }, $results);
    }
}
