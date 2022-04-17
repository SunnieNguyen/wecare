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
namespace WappoVendor\Doctrine\DBAL\Schema\Visitor;

use WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use WappoVendor\Doctrine\DBAL\Schema\Table;
use WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use WappoVendor\Doctrine\DBAL\Schema\Sequence;
class CreateSchemaSqlCollector extends \WappoVendor\Doctrine\DBAL\Schema\Visitor\AbstractVisitor
{
    /**
     * @var array
     */
    private $createNamespaceQueries = array();
    /**
     * @var array
     */
    private $createTableQueries = array();
    /**
     * @var array
     */
    private $createSequenceQueries = array();
    /**
     * @var array
     */
    private $createFkConstraintQueries = array();
    /**
     *
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    private $platform = null;
    /**
     * @param AbstractPlatform $platform
     */
    public function __construct(\WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }
    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName)
    {
        if ($this->platform->supportsSchemas()) {
            $this->createNamespaceQueries = \array_merge($this->createNamespaceQueries, (array) $this->platform->getCreateSchemaSQL($namespaceName));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\WappoVendor\Doctrine\DBAL\Schema\Table $table)
    {
        $this->createTableQueries = \array_merge($this->createTableQueries, (array) $this->platform->getCreateTableSQL($table));
    }
    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(\WappoVendor\Doctrine\DBAL\Schema\Table $localTable, \WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
        if ($this->platform->supportsForeignKeyConstraints()) {
            $this->createFkConstraintQueries = \array_merge($this->createFkConstraintQueries, (array) $this->platform->getCreateForeignKeySQL($fkConstraint, $localTable));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\WappoVendor\Doctrine\DBAL\Schema\Sequence $sequence)
    {
        $this->createSequenceQueries = \array_merge($this->createSequenceQueries, (array) $this->platform->getCreateSequenceSQL($sequence));
    }
    /**
     * @return void
     */
    public function resetQueries()
    {
        $this->createNamespaceQueries = array();
        $this->createTableQueries = array();
        $this->createSequenceQueries = array();
        $this->createFkConstraintQueries = array();
    }
    /**
     * Gets all queries collected so far.
     *
     * @return array
     */
    public function getQueries()
    {
        $sql = array();
        foreach ($this->createNamespaceQueries as $schemaSql) {
            $sql = \array_merge($sql, (array) $schemaSql);
        }
        foreach ($this->createTableQueries as $schemaSql) {
            $sql = \array_merge($sql, (array) $schemaSql);
        }
        foreach ($this->createSequenceQueries as $schemaSql) {
            $sql = \array_merge($sql, (array) $schemaSql);
        }
        foreach ($this->createFkConstraintQueries as $schemaSql) {
            $sql = \array_merge($sql, (array) $schemaSql);
        }
        return $sql;
    }
}
