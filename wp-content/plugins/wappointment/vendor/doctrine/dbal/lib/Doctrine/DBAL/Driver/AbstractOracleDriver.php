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
namespace WappoVendor\Doctrine\DBAL\Driver;

use WappoVendor\Doctrine\DBAL\Driver;
use WappoVendor\Doctrine\DBAL\Exception;
use WappoVendor\Doctrine\DBAL\Platforms\OraclePlatform;
use WappoVendor\Doctrine\DBAL\Schema\OracleSchemaManager;
/**
 * Abstract base implementation of the {@link Doctrine\DBAL\Driver} interface for Oracle based drivers.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
abstract class AbstractOracleDriver implements \WappoVendor\Doctrine\DBAL\Driver, \WappoVendor\Doctrine\DBAL\Driver\ExceptionConverterDriver
{
    /**
     * {@inheritdoc}
     */
    public function convertException($message, \WappoVendor\Doctrine\DBAL\Driver\DriverException $exception)
    {
        switch ($exception->getErrorCode()) {
            case '1':
            case '2299':
            case '38911':
                return new \WappoVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException($message, $exception);
            case '904':
                return new \WappoVendor\Doctrine\DBAL\Exception\InvalidFieldNameException($message, $exception);
            case '918':
            case '960':
                return new \WappoVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException($message, $exception);
            case '923':
                return new \WappoVendor\Doctrine\DBAL\Exception\SyntaxErrorException($message, $exception);
            case '942':
                return new \WappoVendor\Doctrine\DBAL\Exception\TableNotFoundException($message, $exception);
            case '955':
                return new \WappoVendor\Doctrine\DBAL\Exception\TableExistsException($message, $exception);
            case '1017':
            case '12545':
                return new \WappoVendor\Doctrine\DBAL\Exception\ConnectionException($message, $exception);
            case '1400':
                return new \WappoVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException($message, $exception);
            case '2266':
            case '2291':
            case '2292':
                return new \WappoVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException($message, $exception);
        }
        return new \WappoVendor\Doctrine\DBAL\Exception\DriverException($message, $exception);
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabase(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();
        return $params['user'];
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \WappoVendor\Doctrine\DBAL\Platforms\OraclePlatform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\OracleSchemaManager($conn);
    }
    /**
     * Returns an appropriate Easy Connect String for the given parameters.
     *
     * @param array $params The connection parameters to return the Easy Connect STring for.
     *
     * @return string
     *
     * @link http://download.oracle.com/docs/cd/E11882_01/network.112/e10836/naming.htm
     */
    protected function getEasyConnectString(array $params)
    {
        if (!empty($params['host'])) {
            if (!isset($params['port'])) {
                $params['port'] = 1521;
            }
            $serviceName = $params['dbname'];
            if (!empty($params['servicename'])) {
                $serviceName = $params['servicename'];
            }
            $service = 'SID=' . $serviceName;
            $pooled = '';
            $instance = '';
            if (isset($params['service']) && $params['service'] == true) {
                $service = 'SERVICE_NAME=' . $serviceName;
            }
            if (isset($params['instancename']) && !empty($params['instancename'])) {
                $instance = '(INSTANCE_NAME = ' . $params['instancename'] . ')';
            }
            if (isset($params['pooled']) && $params['pooled'] == true) {
                $pooled = '(SERVER=POOLED)';
            }
            return '(DESCRIPTION=' . '(ADDRESS=(PROTOCOL=TCP)(HOST=' . $params['host'] . ')(PORT=' . $params['port'] . '))' . '(CONNECT_DATA=(' . $service . ')' . $instance . $pooled . '))';
        }
        return isset($params['dbname']) ? $params['dbname'] : '';
    }
}
