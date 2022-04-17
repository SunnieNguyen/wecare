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
namespace WappoVendor\Doctrine\DBAL\Driver\SQLSrv;

use WappoVendor\Doctrine\DBAL\Driver\Connection;
use WappoVendor\Doctrine\DBAL\Driver\ServerInfoAwareConnection;
/**
 * SQL Server implementation for the Connection interface.
 *
 * @since 2.3
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class SQLSrvConnection implements \WappoVendor\Doctrine\DBAL\Driver\Connection, \WappoVendor\Doctrine\DBAL\Driver\ServerInfoAwareConnection
{
    /**
     * @var resource
     */
    protected $conn;
    /**
     * @var \Doctrine\DBAL\Driver\SQLSrv\LastInsertId
     */
    protected $lastInsertId;
    /**
     * @param string $serverName
     * @param array  $connectionOptions
     *
     * @throws \Doctrine\DBAL\Driver\SQLSrv\SQLSrvException
     */
    public function __construct($serverName, $connectionOptions)
    {
        if (!\sqlsrv_configure('WarningsReturnAsErrors', 0)) {
            throw \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException::fromSqlSrvErrors();
        }
        $this->conn = \sqlsrv_connect($serverName, $connectionOptions);
        if (!$this->conn) {
            throw \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException::fromSqlSrvErrors();
        }
        $this->lastInsertId = new \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\LastInsertId();
    }
    /**
     * {@inheritdoc}
     */
    public function getServerVersion()
    {
        $serverInfo = \sqlsrv_server_info($this->conn);
        return $serverInfo['SQLServerVersion'];
    }
    /**
     * {@inheritdoc}
     */
    public function requiresQueryForServerVersion()
    {
        return false;
    }
    /**
     * {@inheritDoc}
     */
    public function prepare($sql)
    {
        return new \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\SQLSrvStatement($this->conn, $sql, $this->lastInsertId);
    }
    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $args = \func_get_args();
        $sql = $args[0];
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }
    /**
     * {@inheritDoc}
     * @license New BSD, code from Zend Framework
     */
    public function quote($value, $type = \PDO::PARAM_STR)
    {
        if (\is_int($value)) {
            return $value;
        } elseif (\is_float($value)) {
            return \sprintf('%F', $value);
        }
        return "'" . \str_replace("'", "''", $value) . "'";
    }
    /**
     * {@inheritDoc}
     */
    public function exec($statement)
    {
        $stmt = $this->prepare($statement);
        $stmt->execute();
        return $stmt->rowCount();
    }
    /**
     * {@inheritDoc}
     */
    public function lastInsertId($name = null)
    {
        if ($name !== null) {
            $stmt = $this->prepare('SELECT CONVERT(VARCHAR(MAX), current_value) FROM sys.sequences WHERE name = ?');
            $stmt->execute(array($name));
            return $stmt->fetchColumn();
        }
        return $this->lastInsertId->getId();
    }
    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        if (!\sqlsrv_begin_transaction($this->conn)) {
            throw \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException::fromSqlSrvErrors();
        }
    }
    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        if (!\sqlsrv_commit($this->conn)) {
            throw \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException::fromSqlSrvErrors();
        }
    }
    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
        if (!\sqlsrv_rollback($this->conn)) {
            throw \WappoVendor\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException::fromSqlSrvErrors();
        }
    }
    /**
     * {@inheritDoc}
     */
    public function errorCode()
    {
        $errors = \sqlsrv_errors(\SQLSRV_ERR_ERRORS);
        if ($errors) {
            return $errors[0]['code'];
        }
        return false;
    }
    /**
     * {@inheritDoc}
     */
    public function errorInfo()
    {
        return \sqlsrv_errors(\SQLSRV_ERR_ERRORS);
    }
}
