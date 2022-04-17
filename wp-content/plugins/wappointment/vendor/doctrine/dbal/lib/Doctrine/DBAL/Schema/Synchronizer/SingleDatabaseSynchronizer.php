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
namespace WappoVendor\Doctrine\DBAL\Schema\Synchronizer;

use WappoVendor\Doctrine\DBAL\Connection;
use WappoVendor\Doctrine\DBAL\Schema\Schema;
use WappoVendor\Doctrine\DBAL\Schema\Comparator;
use WappoVendor\Doctrine\DBAL\Schema\Visitor\DropSchemaSqlCollector;
/**
 * Schema Synchronizer for Default DBAL Connection.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class SingleDatabaseSynchronizer extends \WappoVendor\Doctrine\DBAL\Schema\Synchronizer\AbstractSchemaSynchronizer
{
    /**
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    private $platform;
    /**
     * @param \Doctrine\DBAL\Connection $conn
     */
    public function __construct(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        parent::__construct($conn);
        $this->platform = $conn->getDatabasePlatform();
    }
    /**
     * {@inheritdoc}
     */
    public function getCreateSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $createSchema)
    {
        return $createSchema->toSql($this->platform);
    }
    /**
     * {@inheritdoc}
     */
    public function getUpdateSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $toSchema, $noDrops = false)
    {
        $comparator = new \WappoVendor\Doctrine\DBAL\Schema\Comparator();
        $sm = $this->conn->getSchemaManager();
        $fromSchema = $sm->createSchema();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        if ($noDrops) {
            return $schemaDiff->toSaveSql($this->platform);
        }
        return $schemaDiff->toSql($this->platform);
    }
    /**
     * {@inheritdoc}
     */
    public function getDropSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $dropSchema)
    {
        $visitor = new \WappoVendor\Doctrine\DBAL\Schema\Visitor\DropSchemaSqlCollector($this->platform);
        $sm = $this->conn->getSchemaManager();
        $fullSchema = $sm->createSchema();
        foreach ($fullSchema->getTables() as $table) {
            if ($dropSchema->hasTable($table->getName())) {
                $visitor->acceptTable($table);
            }
            foreach ($table->getForeignKeys() as $foreignKey) {
                if (!$dropSchema->hasTable($table->getName())) {
                    continue;
                }
                if (!$dropSchema->hasTable($foreignKey->getForeignTableName())) {
                    continue;
                }
                $visitor->acceptForeignKey($table, $foreignKey);
            }
        }
        if (!$this->platform->supportsSequences()) {
            return $visitor->getQueries();
        }
        foreach ($dropSchema->getSequences() as $sequence) {
            $visitor->acceptSequence($sequence);
        }
        foreach ($dropSchema->getTables() as $table) {
            if (!$table->hasPrimaryKey()) {
                continue;
            }
            $columns = $table->getPrimaryKey()->getColumns();
            if (\count($columns) > 1) {
                continue;
            }
            $checkSequence = $table->getName() . "_" . $columns[0] . "_seq";
            if ($fullSchema->hasSequence($checkSequence)) {
                $visitor->acceptSequence($fullSchema->getSequence($checkSequence));
            }
        }
        return $visitor->getQueries();
    }
    /**
     * {@inheritdoc}
     */
    public function getDropAllSchema()
    {
        $sm = $this->conn->getSchemaManager();
        $visitor = new \WappoVendor\Doctrine\DBAL\Schema\Visitor\DropSchemaSqlCollector($this->platform);
        /* @var $schema \Doctrine\DBAL\Schema\Schema */
        $schema = $sm->createSchema();
        $schema->visit($visitor);
        return $visitor->getQueries();
    }
    /**
     * {@inheritdoc}
     */
    public function createSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $createSchema)
    {
        $this->processSql($this->getCreateSchema($createSchema));
    }
    /**
     * {@inheritdoc}
     */
    public function updateSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $toSchema, $noDrops = false)
    {
        $this->processSql($this->getUpdateSchema($toSchema, $noDrops));
    }
    /**
     * {@inheritdoc}
     */
    public function dropSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $dropSchema)
    {
        $this->processSqlSafely($this->getDropSchema($dropSchema));
    }
    /**
     * {@inheritdoc}
     */
    public function dropAllSchema()
    {
        $this->processSql($this->getDropAllSchema());
    }
}
