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

use WappoVendor\Doctrine\DBAL\DBALException;
use WappoVendor\Doctrine\DBAL\Driver\DriverException;
use WappoVendor\Doctrine\DBAL\Types\Type;
/**
 * SQL Server Schema Manager.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @author  Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author  Lukas Smith <smith@pooteeweet.org> (PEAR MDB2 library)
 * @author  Juozas Kaziukenas <juozas@juokaz.com>
 * @author  Steve Müller <st.mueller@dzh-online.de>
 * @since   2.0
 */
class SQLServerSchemaManager extends \WappoVendor\Doctrine\DBAL\Schema\AbstractSchemaManager
{
    /**
     * {@inheritdoc}
     */
    public function dropDatabase($database)
    {
        try {
            parent::dropDatabase($database);
        } catch (\WappoVendor\Doctrine\DBAL\DBALException $exception) {
            $exception = $exception->getPrevious();
            if (!$exception instanceof \WappoVendor\Doctrine\DBAL\Driver\DriverException) {
                throw $exception;
            }
            // If we have a error code 3702, the drop database operation failed
            // because of active connections on the database.
            // To force dropping the database, we first have to close all active connections
            // on that database and issue the drop database operation again.
            if ($exception->getErrorCode() !== 3702) {
                throw $exception;
            }
            $this->closeActiveDatabaseConnections($database);
            parent::dropDatabase($database);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequenceDefinition($sequence)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\Sequence($sequence['name'], $sequence['increment'], $sequence['start_value']);
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $dbType = \strtok($tableColumn['type'], '(), ');
        $fixed = null;
        $length = (int) $tableColumn['length'];
        $default = $tableColumn['default'];
        if (!isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }
        while ($default != ($default2 = \preg_replace("/^\\((.*)\\)\$/", '$1', $default))) {
            $default = \trim($default2, "'");
            if ($default == 'getdate()') {
                $default = $this->_platform->getCurrentTimestampSQL();
            }
        }
        switch ($dbType) {
            case 'nchar':
            case 'nvarchar':
            case 'ntext':
                // Unicode data requires 2 bytes per character
                $length = $length / 2;
                break;
            case 'varchar':
                // TEXT type is returned as VARCHAR(MAX) with a length of -1
                if ($length == -1) {
                    $dbType = 'text';
                }
                break;
        }
        if ('char' === $dbType || 'nchar' === $dbType || 'binary' === $dbType) {
            $fixed = true;
        }
        $type = $this->_platform->getDoctrineTypeMapping($dbType);
        $type = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
        $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);
        $options = array('length' => $length == 0 || !\in_array($type, array('text', 'string')) ? null : $length, 'unsigned' => false, 'fixed' => (bool) $fixed, 'default' => $default !== 'NULL' ? $default : null, 'notnull' => (bool) $tableColumn['notnull'], 'scale' => $tableColumn['scale'], 'precision' => $tableColumn['precision'], 'autoincrement' => (bool) $tableColumn['autoincrement'], 'comment' => $tableColumn['comment'] !== '' ? $tableColumn['comment'] : null);
        $column = new \WappoVendor\Doctrine\DBAL\Schema\Column($tableColumn['name'], \WappoVendor\Doctrine\DBAL\Types\Type::getType($type), $options);
        if (isset($tableColumn['collation']) && $tableColumn['collation'] !== 'NULL') {
            $column->setPlatformOption('collation', $tableColumn['collation']);
        }
        return $column;
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeysList($tableForeignKeys)
    {
        $foreignKeys = array();
        foreach ($tableForeignKeys as $tableForeignKey) {
            if (!isset($foreignKeys[$tableForeignKey['ForeignKey']])) {
                $foreignKeys[$tableForeignKey['ForeignKey']] = array('local_columns' => array($tableForeignKey['ColumnName']), 'foreign_table' => $tableForeignKey['ReferenceTableName'], 'foreign_columns' => array($tableForeignKey['ReferenceColumnName']), 'name' => $tableForeignKey['ForeignKey'], 'options' => array('onUpdate' => \str_replace('_', ' ', $tableForeignKey['update_referential_action_desc']), 'onDelete' => \str_replace('_', ' ', $tableForeignKey['delete_referential_action_desc'])));
            } else {
                $foreignKeys[$tableForeignKey['ForeignKey']]['local_columns'][] = $tableForeignKey['ColumnName'];
                $foreignKeys[$tableForeignKey['ForeignKey']]['foreign_columns'][] = $tableForeignKey['ReferenceColumnName'];
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
            $tableIndex['non_unique'] = (bool) $tableIndex['non_unique'];
            $tableIndex['primary'] = (bool) $tableIndex['primary'];
            $tableIndex['flags'] = $tableIndex['flags'] ? array($tableIndex['flags']) : null;
        }
        return parent::_getPortableTableIndexesList($tableIndexRows, $tableName);
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
    protected function _getPortableTableDefinition($table)
    {
        return $table['name'];
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
    protected function getPortableNamespaceDefinition(array $namespace)
    {
        return $namespace['name'];
    }
    /**
     * {@inheritdoc}
     */
    protected function _getPortableViewDefinition($view)
    {
        // @todo
        return new \WappoVendor\Doctrine\DBAL\Schema\View($view['name'], null);
    }
    /**
     * {@inheritdoc}
     */
    public function listTableIndexes($table)
    {
        $sql = $this->_platform->getListTableIndexesSQL($table, $this->_conn->getDatabase());
        try {
            $tableIndexes = $this->_conn->fetchAll($sql);
        } catch (\PDOException $e) {
            if ($e->getCode() == "IMSSP") {
                return array();
            } else {
                throw $e;
            }
        } catch (\WappoVendor\Doctrine\DBAL\DBALException $e) {
            if (\strpos($e->getMessage(), 'SQLSTATE [01000, 15472]') === 0) {
                return array();
            } else {
                throw $e;
            }
        }
        return $this->_getPortableTableIndexesList($tableIndexes, $table);
    }
    /**
     * {@inheritdoc}
     */
    public function alterTable(\WappoVendor\Doctrine\DBAL\Schema\TableDiff $tableDiff)
    {
        if (\count($tableDiff->removedColumns) > 0) {
            foreach ($tableDiff->removedColumns as $col) {
                $columnConstraintSql = $this->getColumnConstraintSQL($tableDiff->name, $col->getName());
                foreach ($this->_conn->fetchAll($columnConstraintSql) as $constraint) {
                    $this->_conn->exec("ALTER TABLE {$tableDiff->name} DROP CONSTRAINT " . $constraint['Name']);
                }
            }
        }
        parent::alterTable($tableDiff);
    }
    /**
     * Returns the SQL to retrieve the constraints for a given column.
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    private function getColumnConstraintSQL($table, $column)
    {
        return "SELECT SysObjects.[Name]\n            FROM SysObjects INNER JOIN (SELECT [Name],[ID] FROM SysObjects WHERE XType = 'U') AS Tab\n            ON Tab.[ID] = Sysobjects.[Parent_Obj]\n            INNER JOIN sys.default_constraints DefCons ON DefCons.[object_id] = Sysobjects.[ID]\n            INNER JOIN SysColumns Col ON Col.[ColID] = DefCons.[parent_column_id] AND Col.[ID] = Tab.[ID]\n            WHERE Col.[Name] = " . $this->_conn->quote($column) . " AND Tab.[Name] = " . $this->_conn->quote($table) . "\n            ORDER BY Col.[Name]";
    }
    /**
     * Closes currently active connections on the given database.
     *
     * This is useful to force DROP DATABASE operations which could fail because of active connections.
     *
     * @param string $database The name of the database to close currently active connections for.
     *
     * @return void
     */
    private function closeActiveDatabaseConnections($database)
    {
        $database = new \WappoVendor\Doctrine\DBAL\Schema\Identifier($database);
        $this->_execSql(\sprintf('ALTER DATABASE %s SET SINGLE_USER WITH ROLLBACK IMMEDIATE', $database->getQuotedName($this->_platform)));
    }
}
