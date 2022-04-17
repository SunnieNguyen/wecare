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
use WappoVendor\Doctrine\DBAL\Schema\SchemaException;
/**
 * Gathers SQL statements that allow to completely drop the current schema.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class DropSchemaSqlCollector extends \WappoVendor\Doctrine\DBAL\Schema\Visitor\AbstractVisitor
{
    /**
     * @var \SplObjectStorage
     */
    private $constraints;
    /**
     * @var \SplObjectStorage
     */
    private $sequences;
    /**
     * @var \SplObjectStorage
     */
    private $tables;
    /**
     * @var AbstractPlatform
     */
    private $platform;
    /**
     * @param AbstractPlatform $platform
     */
    public function __construct(\WappoVendor\Doctrine\DBAL\Platforms\AbstractPlatform $platform)
    {
        $this->platform = $platform;
        $this->clearQueries();
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\WappoVendor\Doctrine\DBAL\Schema\Table $table)
    {
        $this->tables->attach($table);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(\WappoVendor\Doctrine\DBAL\Schema\Table $localTable, \WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
        if (\strlen($fkConstraint->getName()) == 0) {
            throw \WappoVendor\Doctrine\DBAL\Schema\SchemaException::namedForeignKeyRequired($localTable, $fkConstraint);
        }
        $this->constraints->attach($fkConstraint, $localTable);
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\WappoVendor\Doctrine\DBAL\Schema\Sequence $sequence)
    {
        $this->sequences->attach($sequence);
    }
    /**
     * @return void
     */
    public function clearQueries()
    {
        $this->constraints = new \SplObjectStorage();
        $this->sequences = new \SplObjectStorage();
        $this->tables = new \SplObjectStorage();
    }
    /**
     * @return array
     */
    public function getQueries()
    {
        $sql = array();
        foreach ($this->constraints as $fkConstraint) {
            $localTable = $this->constraints[$fkConstraint];
            $sql[] = $this->platform->getDropForeignKeySQL($fkConstraint, $localTable);
        }
        foreach ($this->sequences as $sequence) {
            $sql[] = $this->platform->getDropSequenceSQL($sequence);
        }
        foreach ($this->tables as $table) {
            $sql[] = $this->platform->getDropTableSQL($table);
        }
        return $sql;
    }
}
