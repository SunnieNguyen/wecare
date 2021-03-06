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
namespace WappoVendor\Doctrine\DBAL\Id;

use WappoVendor\Doctrine\DBAL\Schema\Table;
use WappoVendor\Doctrine\DBAL\Schema\Schema;
use WappoVendor\Doctrine\DBAL\Schema\Column;
use WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use WappoVendor\Doctrine\DBAL\Schema\Sequence;
use WappoVendor\Doctrine\DBAL\Schema\Index;
class TableGeneratorSchemaVisitor implements \WappoVendor\Doctrine\DBAL\Schema\Visitor\Visitor
{
    /**
     * @var string
     */
    private $generatorTableName;
    /**
     * @param string $generatorTableName
     */
    public function __construct($generatorTableName = 'sequences')
    {
        $this->generatorTableName = $generatorTableName;
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->createTable($this->generatorTableName);
        $table->addColumn('sequence_name', 'string');
        $table->addColumn('sequence_value', 'integer', array('default' => 1));
        $table->addColumn('sequence_increment_by', 'integer', array('default' => 1));
    }
    /**
     * {@inheritdoc}
     */
    public function acceptTable(\WappoVendor\Doctrine\DBAL\Schema\Table $table)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptColumn(\WappoVendor\Doctrine\DBAL\Schema\Table $table, \WappoVendor\Doctrine\DBAL\Schema\Column $column)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(\WappoVendor\Doctrine\DBAL\Schema\Table $localTable, \WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptIndex(\WappoVendor\Doctrine\DBAL\Schema\Table $table, \WappoVendor\Doctrine\DBAL\Schema\Index $index)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptSequence(\WappoVendor\Doctrine\DBAL\Schema\Sequence $sequence)
    {
    }
}
