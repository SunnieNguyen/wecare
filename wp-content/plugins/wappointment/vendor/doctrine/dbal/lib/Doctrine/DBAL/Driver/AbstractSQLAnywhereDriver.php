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

use WappoVendor\Doctrine\DBAL\DBALException;
use WappoVendor\Doctrine\DBAL\Driver;
use WappoVendor\Doctrine\DBAL\Exception;
use WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere11Platform;
use WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere12Platform;
use WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere16Platform;
use WappoVendor\Doctrine\DBAL\Platforms\SQLAnywherePlatform;
use WappoVendor\Doctrine\DBAL\Schema\SQLAnywhereSchemaManager;
use WappoVendor\Doctrine\DBAL\VersionAwarePlatformDriver;
/**
 * Abstract base implementation of the {@link Doctrine\DBAL\Driver} interface for SAP Sybase SQL Anywhere based drivers.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
abstract class AbstractSQLAnywhereDriver implements \WappoVendor\Doctrine\DBAL\Driver, \WappoVendor\Doctrine\DBAL\Driver\ExceptionConverterDriver, \WappoVendor\Doctrine\DBAL\VersionAwarePlatformDriver
{
    /**
     * {@inheritdoc}
     *
     * @link http://dcx.sybase.com/index.html#sa160/en/saerrors/sqlerror.html
     */
    public function convertException($message, \WappoVendor\Doctrine\DBAL\Driver\DriverException $exception)
    {
        switch ($exception->getErrorCode()) {
            case '-100':
            case '-103':
            case '-832':
                return new \WappoVendor\Doctrine\DBAL\Exception\ConnectionException($message, $exception);
            case '-143':
                return new \WappoVendor\Doctrine\DBAL\Exception\InvalidFieldNameException($message, $exception);
            case '-193':
            case '-196':
                return new \WappoVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException($message, $exception);
            case '-194':
            case '-198':
                return new \WappoVendor\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException($message, $exception);
            case '-144':
                return new \WappoVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException($message, $exception);
            case '-184':
            case '-195':
                return new \WappoVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException($message, $exception);
            case '-131':
                return new \WappoVendor\Doctrine\DBAL\Exception\SyntaxErrorException($message, $exception);
            case '-110':
                return new \WappoVendor\Doctrine\DBAL\Exception\TableExistsException($message, $exception);
            case '-141':
            case '-1041':
                return new \WappoVendor\Doctrine\DBAL\Exception\TableNotFoundException($message, $exception);
        }
        return new \WappoVendor\Doctrine\DBAL\Exception\DriverException($message, $exception);
    }
    /**
     * {@inheritdoc}
     */
    public function createDatabasePlatformForVersion($version)
    {
        if (!\preg_match('/^(?P<major>\\d+)(?:\\.(?P<minor>\\d+)(?:\\.(?P<patch>\\d+)(?:\\.(?P<build>\\d+))?)?)?/', $version, $versionParts)) {
            throw \WappoVendor\Doctrine\DBAL\DBALException::invalidPlatformVersionSpecified($version, '<major_version>.<minor_version>.<patch_version>.<build_version>');
        }
        $majorVersion = $versionParts['major'];
        $minorVersion = isset($versionParts['minor']) ? $versionParts['minor'] : 0;
        $patchVersion = isset($versionParts['patch']) ? $versionParts['patch'] : 0;
        $buildVersion = isset($versionParts['build']) ? $versionParts['build'] : 0;
        $version = $majorVersion . '.' . $minorVersion . '.' . $patchVersion . '.' . $buildVersion;
        switch (true) {
            case \version_compare($version, '16', '>='):
                return new \WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere16Platform();
            case \version_compare($version, '12', '>='):
                return new \WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere12Platform();
            case \version_compare($version, '11', '>='):
                return new \WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere11Platform();
            default:
                return new \WappoVendor\Doctrine\DBAL\Platforms\SQLAnywherePlatform();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabase(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();
        if (isset($params['dbname'])) {
            return $params['dbname'];
        }
        return $conn->query('SELECT DB_NAME()')->fetchColumn();
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \WappoVendor\Doctrine\DBAL\Platforms\SQLAnywhere12Platform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\SQLAnywhereSchemaManager($conn);
    }
}
