<?php

namespace WappoVendor\Illuminate\Database\Connectors;

use PDOException;
use WappoVendor\Illuminate\Support\Arr;
use InvalidArgumentException;
use WappoVendor\Illuminate\Database\Connection;
use WappoVendor\Illuminate\Database\MySqlConnection;
use WappoVendor\Illuminate\Database\SQLiteConnection;
use WappoVendor\Illuminate\Database\PostgresConnection;
use WappoVendor\Illuminate\Database\SqlServerConnection;
use WappoVendor\Illuminate\Contracts\Container\Container;
use WappoVendor\Illuminate\Contracts\Debug\ExceptionHandler;
class ConnectionFactory
{
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;
    /**
     * Create a new connection factory instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function __construct(\WappoVendor\Illuminate\Contracts\Container\Container $container)
    {
        $this->container = $container;
    }
    /**
     * Establish a PDO connection based on the configuration.
     *
     * @param  array   $config
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);
        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }
        return $this->createSingleConnection($config);
    }
    /**
     * Parse and prepare the database configuration.
     *
     * @param  array   $config
     * @param  string  $name
     * @return array
     */
    protected function parseConfig(array $config, $name)
    {
        return \WappoVendor\Illuminate\Support\Arr::add(\WappoVendor\Illuminate\Support\Arr::add($config, 'prefix', ''), 'name', $name);
    }
    /**
     * Create a single database connection instance.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connection
     */
    protected function createSingleConnection(array $config)
    {
        $pdo = $this->createPdoResolver($config);
        return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
    }
    /**
     * Create a single database connection instance.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connection
     */
    protected function createReadWriteConnection(array $config)
    {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));
        return $connection->setReadPdo($this->createReadPdo($config));
    }
    /**
     * Create a new PDO instance for reading.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createReadPdo(array $config)
    {
        return $this->createPdoResolver($this->getReadConfig($config));
    }
    /**
     * Get the read configuration for a read / write connection.
     *
     * @param  array  $config
     * @return array
     */
    protected function getReadConfig(array $config)
    {
        return $this->mergeReadWriteConfig($config, $this->getReadWriteConfig($config, 'read'));
    }
    /**
     * Get the read configuration for a read / write connection.
     *
     * @param  array  $config
     * @return array
     */
    protected function getWriteConfig(array $config)
    {
        return $this->mergeReadWriteConfig($config, $this->getReadWriteConfig($config, 'write'));
    }
    /**
     * Get a read / write level configuration.
     *
     * @param  array   $config
     * @param  string  $type
     * @return array
     */
    protected function getReadWriteConfig(array $config, $type)
    {
        return isset($config[$type][0]) ? \WappoVendor\Illuminate\Support\Arr::random($config[$type]) : $config[$type];
    }
    /**
     * Merge a configuration for a read / write connection.
     *
     * @param  array  $config
     * @param  array  $merge
     * @return array
     */
    protected function mergeReadWriteConfig(array $config, array $merge)
    {
        return \WappoVendor\Illuminate\Support\Arr::except(\array_merge($config, $merge), ['read', 'write']);
    }
    /**
     * Create a new Closure that resolves to a PDO instance.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createPdoResolver(array $config)
    {
        return \array_key_exists('host', $config) ? $this->createPdoResolverWithHosts($config) : $this->createPdoResolverWithoutHosts($config);
    }
    /**
     * Create a new Closure that resolves to a PDO instance with a specific host or an array of hosts.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createPdoResolverWithHosts(array $config)
    {
        return function () use($config) {
            foreach (\WappoVendor\Illuminate\Support\Arr::shuffle($hosts = $this->parseHosts($config)) as $key => $host) {
                $config['host'] = $host;
                try {
                    return $this->createConnector($config)->connect($config);
                } catch (\PDOException $e) {
                    if (\count($hosts) - 1 === $key && $this->container->bound(\WappoVendor\Illuminate\Contracts\Debug\ExceptionHandler::class)) {
                        $this->container->make(\WappoVendor\Illuminate\Contracts\Debug\ExceptionHandler::class)->report($e);
                    }
                }
            }
            throw $e;
        };
    }
    /**
     * Parse the hosts configuration item into an array.
     *
     * @param  array  $config
     * @return array
     */
    protected function parseHosts(array $config)
    {
        $hosts = \WappoVendor\Illuminate\Support\Arr::wrap($config['host']);
        if (empty($hosts)) {
            throw new \InvalidArgumentException('Database hosts array is empty.');
        }
        return $hosts;
    }
    /**
     * Create a new Closure that resolves to a PDO instance where there is no configured host.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createPdoResolverWithoutHosts(array $config)
    {
        return function () use($config) {
            return $this->createConnector($config)->connect($config);
        };
    }
    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createConnector(array $config)
    {
        if (!isset($config['driver'])) {
            throw new \InvalidArgumentException('A driver must be specified.');
        }
        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }
        switch ($config['driver']) {
            case 'mysql':
                return new \WappoVendor\Illuminate\Database\Connectors\MySqlConnector();
            case 'pgsql':
                return new \WappoVendor\Illuminate\Database\Connectors\PostgresConnector();
            case 'sqlite':
                return new \WappoVendor\Illuminate\Database\Connectors\SQLiteConnector();
            case 'sqlsrv':
                return new \WappoVendor\Illuminate\Database\Connectors\SqlServerConnector();
        }
        throw new \InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }
    /**
     * Create a new connection instance.
     *
     * @param  string   $driver
     * @param  \PDO|\Closure     $connection
     * @param  string   $database
     * @param  string   $prefix
     * @param  array    $config
     * @return \Illuminate\Database\Connection
     *
     * @throws \InvalidArgumentException
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = \WappoVendor\Illuminate\Database\Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }
        switch ($driver) {
            case 'mysql':
                return new \WappoVendor\Illuminate\Database\MySqlConnection($connection, $database, $prefix, $config);
            case 'pgsql':
                return new \WappoVendor\Illuminate\Database\PostgresConnection($connection, $database, $prefix, $config);
            case 'sqlite':
                return new \WappoVendor\Illuminate\Database\SQLiteConnection($connection, $database, $prefix, $config);
            case 'sqlsrv':
                return new \WappoVendor\Illuminate\Database\SqlServerConnection($connection, $database, $prefix, $config);
        }
        throw new \InvalidArgumentException("Unsupported driver [{$driver}]");
    }
}
