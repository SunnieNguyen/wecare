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

use WappoVendor\Doctrine\DBAL\Schema\Table;
use WappoVendor\Doctrine\DBAL\Schema\Schema;
use WappoVendor\Doctrine\DBAL\Schema\Column;
use WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint;
use WappoVendor\Doctrine\DBAL\Schema\Sequence;
use WappoVendor\Doctrine\DBAL\Schema\Index;
/**
 * Abstract Visitor with empty methods for easy extension.
 */
class AbstractVisitor implements \WappoVendor\Doctrine\DBAL\Schema\Visitor\Visitor, \WappoVendor\Doctrine\DBAL\Schema\Visitor\NamespaceVisitor
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function acceptSchema(\WappoVendor\Doctrine\DBAL\Schema\Schema $schema)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function acceptNamespace($namespaceName)
    {
    }
    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     */
    public function acceptTable(\WappoVendor\Doctrine\DBAL\Schema\Table $table)
    {
    }
    /**
     * @param \Doctrine\DBAL\Schema\Table  $table
     * @param \Doctrine\DBAL\Schema\Column $column
     */
    public function acceptColumn(\WappoVendor\Doctrine\DBAL\Schema\Table $table, \WappoVendor\Doctrine\DBAL\Schema\Column $column)
    {
    }
    /**
     * @param \Doctrine\DBAL\Schema\Table                $localTable
     * @param \Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint
     */
    public function acceptForeignKey(\WappoVendor\Doctrine\DBAL\Schema\Table $localTable, \WappoVendor\Doctrine\DBAL\Schema\ForeignKeyConstraint $fkConstraint)
    {
    }
    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     * @param \Doctrine\DBAL\Schema\Index $index
     */
    public function acceptIndex(\WappoVendor\Doctrine\DBAL\Schema\Table $table, \WappoVendor\Doctrine\DBAL\Schema\Index $index)
    {
    }
    /**
     * @param \Doctrine\DBAL\Schema\Sequence $sequence
     */
    public function acceptSequence(\WappoVendor\Doctrine\DBAL\Schema\Sequence $sequence)
    {
    }
}
