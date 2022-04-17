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
use WappoVendor\Doctrine\DBAL\Platforms\SqlitePlatform;
use WappoVendor\Doctrine\DBAL\Schema\SqliteSchemaManager;
/**
 * Abstract base implementation of the {@link Doctrine\DBAL\Driver} interface for SQLite based drivers.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
abstract class AbstractSQLiteDriver implements \WappoVendor\Doctrine\DBAL\Driver, \WappoVendor\Doctrine\DBAL\Driver\ExceptionConverterDriver
{
    /**
     * {@inheritdoc}
     *
     * @link http://www.sqlite.org/c3ref/c_abort.html
     */
    public function convertException($message, \WappoVendor\Doctrine\DBAL\Driver\DriverException $exception)
    {
        if (\strpos($exception->getMessage(), 'must be unique') !== false || \strpos($exception->getMessage(), 'is not unique') !== false || \strpos($exception->getMessage(), 'are not unique') !== false || \strpos($exception->getMessage(), 'UNIQUE constraint failed') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\UniqueConstraintViolationException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'may not be NULL') !== false || \strpos($exception->getMessage(), 'NOT NULL constraint failed') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\NotNullConstraintViolationException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'no such table:') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\TableNotFoundException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'already exists') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\TableExistsException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'has no column named') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\InvalidFieldNameException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'ambiguous column name') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\NonUniqueFieldNameException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'syntax error') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\SyntaxErrorException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'attempt to write a readonly database') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\ReadOnlyException($message, $exception);
        }
        if (\strpos($exception->getMessage(), 'unable to open database file') !== false) {
            return new \WappoVendor\Doctrine\DBAL\Exception\ConnectionException($message, $exception);
        }
        return new \WappoVendor\Doctrine\DBAL\Exception\DriverException($message, $exception);
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabase(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();
        return isset($params['path']) ? $params['path'] : null;
    }
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new \WappoVendor\Doctrine\DBAL\Platforms\SqlitePlatform();
    }
    /**
     * {@inheritdoc}
     */
    public function getSchemaManager(\WappoVendor\Doctrine\DBAL\Connection $conn)
    {
        return new \WappoVendor\Doctrine\DBAL\Schema\SqliteSchemaManager($conn);
    }
}
