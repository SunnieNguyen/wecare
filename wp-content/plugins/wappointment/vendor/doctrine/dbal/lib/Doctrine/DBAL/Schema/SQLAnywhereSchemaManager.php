<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */
namespace WappoVendor\Doctrine\DBAL\Schema;

use WappoVendor\Doctrine\DBAL\Types\Type;
/**
 * SAP Sybase SQL Anywhere schema manager.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
class SQLAnywhereSchemaManager extends \WappoVendor\Doctrine\DBAL\Schema\AbstractSchemaManager
{
    /**
     * {@inheritdoc}
     *
     * Starts a database after creation
     * as SQL Anywhere needs a database to be started
     * before it can be used.
     *
     * @see startDatabase
     */
    public function createDatabase($database)
    {
        parent::createDatabase($database);
        $this->startDatabase($database);
    }
    /**
     * {@inheritdoc}
     *
     * Tries stopping a database before dropping
     * as SQL Anywhere needs a database to be stopped
     * before it can be dropped.
     *
     * @see stopDatabase
     */
    public function dropDatabase($database)
    {
        $this->tryMethod('stopDatabase', $database);
        parent::dropDatabase($database);
    }
    /**
     * Starts a database.
     *
     * @param string $database The name of the database to start.
     */
    public function startDatabase($database)
    {
        $this->_execSql($this->_platform->getStartDatabaseSQL($database));
    }
    /**
     * Stops a database.
     *
     * @param string $database The name of the database to stop.
     */
    public function stopDatabase($database)
    {
        $this->_execSql($this->_platform->getStopDatabaseSQL($database));
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableDatabaseDefinition($database)
    {
        return $database['name'];
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequenceDefinition($sequence)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\Sequence($sequence['sequence_name'], $sequence['increment_by'], $sequence['start_with']);
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $type = $this->_platform->getDoctrineTypeMapping($tableColumn['type']);
        $type = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
        $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);
        $precision = null;
        $scale = null;
        $fixed = false;
        $default = null;
        if (null !== $tableColumn['default']) {
            // Strip quotes from default value.
            $default = \preg_replace(array("/^'(.*)'\$/", "/''/"), array("\$1", "'"), $tableColumn['default']);
            if ('autoincrement' == $default) {
                $default = null;
            }
        }
        switch ($tableColumn['type']) {
            case 'binary':
            case 'char':
            case 'nchar':
                $fixed = true;
        }
        switch ($type) {
            case 'decimal':
            case 'float':
                $precision = $tableColumn['length'];
                $scale = $tableColumn['scale'];
        }
        return new \WappoVendor\Doctrine\DBAL\Schema\Column($tableColumn['column_name'], \WappoVendor\Doctrine\DBAL\Types\Type::getType($type), array('length' => $type == 'string' ? $tableColumn['length'] : null, 'precision' => $precision, 'scale' => $scale, 'unsigned' => (bool) $tableColumn['unsigned'], 'fixed' => $fixed, 'notnull' => (bool) $tableColumn['notnull'], 'default' => $default, 'autoincrement' => (bool) $tableColumn['autoincrement'], 'comment' => isset($tableColumn['comment']) && '' !== $tableColumn['comment'] ? $tableColumn['comment'] : null));
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableDefinition($table)
    {
        return $table['table_name'];
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeyDefinition($tableForeignKey)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint($tableForeignKey['local_columns'], $tableForeignKey['foreign_table'], $tableForeignKey['foreign_columns'], $tableForeignKey['name'], $tableForeignKey['options']);
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeysList($tableForeignKeys)
    {
        $foreignKeys = array();
        foreach ($tableForeignKeys as $tableForeignKey) {
            if (!isset($foreignKeys[$tableForeignKey['index_name']])) {
                $foreignKeys[$tableForeignKey['index_name']] = array('local_columns' => array($tableForeignKey['local_column']), 'foreign_table' => $tableForeignKey['foreign_table'], 'foreign_columns' => array($tableForeignKey['foreign_column']), 'name' => $tableForeignKey['index_name'], 'options' => array('notnull' => $tableForeignKey['notnull'], 'match' => $tableForeignKey['match'], 'onUpdate' => $tableForeignKey['on_update'], 'onDelete' => $tableForeignKey['on_delete'], 'check_on_commit' => $tableForeignKey['check_on_commit'], 'clustered' => $tableForeignKey['clustered'], 'for_olap_workload' => $tableForeignKey['for_olap_workload']));
            } else {
                $foreignKeys[$tableForeignKey['index_name']]['local_columns'][] = $tableForeignKey['local_column'];
                $foreignKeys[$tableForeignKey['index_name']]['foreign_columns'][] = $tableForeignKey['foreign_column'];
            }
        }
        return parent::_getPortableTableForeignKeysList($foreignKeys);
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableIndexesList($tableIndexRows, $tableName = null)
    {
        foreach ($tableIndexRows as &$tableIndex) {
            $tableIndex['primary'] = (bool) $tableIndex['primary'];
            $tableIndex['flags'] = array();
            if ($tableIndex['clustered']) {
                $tableIndex['flags'][] = 'clustered';
            }
            if ($tableIndex['with_nulls_not_distinct']) {
                $tableIndex['flags'][] = 'with_nulls_not_distinct';
            }
            if ($tableIndex['for_olap_workload']) {
                $tableIndex['flags'][] = 'for_olap_workload';
            }
        }
        return parent::_getPortableTableIndexesList($tableIndexRows, $tableName);
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableViewDefinition($view)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\View($view['table_name'], \preg_replace('/^.*\\s+as\\s+SELECT(.*)/i', "SELECT\$1", $view['view_def']));
    }
}
