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
namespace WappoVendor\Doctrine\DBAL\Driver\PDOSqlsrv;

use WappoVendor\Doctrine\DBAL\Driver\PDOConnection;
/**
 * Sqlsrv Connection implementation.
 *
 * @since 2.0
 */
class Connection extends \WappoVendor\Doctrine\DBAL\Driver\PDOConnection implements \WappoVendor\Doctrine\DBAL\Driver\Connection
{
    /**
     * {@inheritdoc}
     */
    public function __construct($dsn, $user = null, $password = null, array $options = null)
    {
        parent::__construct($dsn, $user, $password, $options);
        $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array(\WappoVendor\Doctrine\DBAL\Driver\PDOSqlsrv\Statement::class, array()));
    }
    /**
     * @override
     */
    public function lastInsertId($name = null)
    {
        if (null === $name) {
            return parent::lastInsertId($name);
        }
        $stmt = $this->prepare('SELECT CONVERT(VARCHAR(MAX), current_value) FROM sys.sequences WHERE name = ?');
        $stmt->execute(array($name));
        return $stmt->fetchColumn();
    }
    /**
     * {@inheritDoc}
     */
    public function quote($value, $type = \PDO::PARAM_STR)
    {
        $val = parent::quote($value, $type);
        // Fix for a driver version terminating all values with null byte
        if (\strpos($val, "\x00") !== false) {
            $val = \substr($val, 0, -1);
        }
        return $val;
    }
}
