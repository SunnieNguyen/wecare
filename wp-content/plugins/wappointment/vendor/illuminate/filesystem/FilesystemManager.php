<?php

namespace WappoVendor\Illuminate\Filesystem;

use Closure;
use WappoVendor\Aws\S3\S3Client;
use WappoVendor\OpenCloud\Rackspace;
use WappoVendor\Illuminate\Support\Arr;
use InvalidArgumentException;
use WappoVendor\League\Flysystem\AdapterInterface;
use WappoVendor\League\Flysystem\FilesystemInterface;
use WappoVendor\League\Flysystem\Cached\CachedAdapter;
use WappoVendor\League\Flysystem\Filesystem as Flysystem;
use WappoVendor\League\Flysystem\Adapter\Ftp as FtpAdapter;
use WappoVendor\League\Flysystem\Rackspace\RackspaceAdapter;
use WappoVendor\League\Flysystem\Adapter\Local as LocalAdapter;
use WappoVendor\League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use WappoVendor\League\Flysystem\Cached\Storage\Memory as MemoryStore;
use WappoVendor\Illuminate\Contracts\Filesystem\Factory as FactoryContract;
/**
 * @mixin \Illuminate\Contracts\Filesystem\Filesystem
 */
class FilesystemManager implements \WappoVendor\Illuminate\Contracts\Filesystem\Factory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * The array of resolved filesystem drivers.
     *
     * @var array
     */
    protected $disks = [];
    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];
    /**
     * Create a new filesystem manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }
    /**
     * Get a filesystem instance.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function drive($name = null)
    {
        return $this->disk($name);
    }
    /**
     * Get a filesystem instance.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        return $this->disks[$name] = $this->get($name);
    }
    /**
     * Get a default cloud filesystem instance.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function cloud()
    {
        $name = $this->getDefaultCloudDriver();
        return $this->disks[$name] = $this->get($name);
    }
    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function get($name)
    {
        return $this->disks[$name] ?? $this->resolve($name);
    }
    /**
     * Resolve the given disk.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }
        $driverMethod = 'create' . \ucfirst($config['driver']) . 'Driver';
        if (\method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new \InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }
    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function callCustomCreator(array $config)
    {
        $driver = $this->customCreators[$config['driver']]($this->app, $config);
        if ($driver instanceof \WappoVendor\League\Flysystem\FilesystemInterface) {
            return $this->adapt($driver);
        }
        return $driver;
    }
    /**
     * Create an instance of the local driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function createLocalDriver(array $config)
    {
        $permissions = $config['permissions'] ?? [];
        $links = ($config['links'] ?? null) === 'skip' ? \WappoVendor\League\Flysystem\Adapter\Local::SKIP_LINKS : \WappoVendor\League\Flysystem\Adapter\Local::DISALLOW_LINKS;
        return $this->adapt($this->createFlysystem(new \WappoVendor\League\Flysystem\Adapter\Local($config['root'], \LOCK_EX, $links, $permissions), $config));
    }
    /**
     * Create an instance of the ftp driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function createFtpDriver(array $config)
    {
        return $this->adapt($this->createFlysystem(new \WappoVendor\League\Flysystem\Adapter\Ftp($config), $config));
    }
    /**
     * Create an instance of the Amazon S3 driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Filesystem\Cloud
     */
    public function createS3Driver(array $config)
    {
        $s3Config = $this->formatS3Config($config);
        $root = $s3Config['root'] ?? null;
        $options = $config['options'] ?? [];
        return $this->adapt($this->createFlysystem(new \WappoVendor\League\Flysystem\AwsS3v3\AwsS3Adapter(new \WappoVendor\Aws\S3\S3Client($s3Config), $s3Config['bucket'], $root, $options), $config));
    }
    /**
     * Format the given S3 configuration with the default options.
     *
     * @param  array  $config
     * @return array
     */
    protected function formatS3Config(array $config)
    {
        $config += ['version' => 'latest'];
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = \WappoVendor\Illuminate\Support\Arr::only($config, ['key', 'secret']);
        }
        return $config;
    }
    /**
     * Create an instance of the Rackspace driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Filesystem\Cloud
     */
    public function createRackspaceDriver(array $config)
    {
        $client = new \WappoVendor\OpenCloud\Rackspace($config['endpoint'], ['username' => $config['username'], 'apiKey' => $config['key']]);
        $root = $config['root'] ?? null;
        return $this->adapt($this->createFlysystem(new \WappoVendor\League\Flysystem\Rackspace\RackspaceAdapter($this->getRackspaceContainer($client, $config), $root), $config));
    }
    /**
     * Get the Rackspace Cloud Files container.
     *
     * @param  \OpenCloud\Rackspace  $client
     * @param  array  $config
     * @return \OpenCloud\ObjectStore\Resource\Container
     */
    protected function getRackspaceContainer(\WappoVendor\OpenCloud\Rackspace $client, array $config)
    {
        $urlType = $config['url_type'] ?? null;
        $store = $client->objectStoreService('cloudFiles', $config['region'], $urlType);
        return $store->getContainer($config['container']);
    }
    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\AdapterInterface  $adapter
     * @param  array  $config
     * @return \League\Flysystem\FilesystemInterface
     */
    protected function createFlysystem(\WappoVendor\League\Flysystem\AdapterInterface $adapter, array $config)
    {
        $cache = \WappoVendor\Illuminate\Support\Arr::pull($config, 'cache');
        $config = \WappoVendor\Illuminate\Support\Arr::only($config, ['visibility', 'disable_asserts', 'url']);
        if ($cache) {
            $adapter = new \WappoVendor\League\Flysystem\Cached\CachedAdapter($adapter, $this->createCacheStore($cache));
        }
        return new \WappoVendor\League\Flysystem\Filesystem($adapter, \count($config) > 0 ? $config : null);
    }
    /**
     * Create a cache store instance.
     *
     * @param  mixed  $config
     * @return \League\Flysystem\Cached\CacheInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function createCacheStore($config)
    {
        if ($config === true) {
            return new \WappoVendor\League\Flysystem\Cached\Storage\Memory();
        }
        return new \WappoVendor\Illuminate\Filesystem\Cache($this->app['cache']->store($config['store']), $config['prefix'] ?? 'flysystem', $config['expire'] ?? null);
    }
    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemInterface  $filesystem
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function adapt(\WappoVendor\League\Flysystem\FilesystemInterface $filesystem)
    {
        return new \WappoVendor\Illuminate\Filesystem\FilesystemAdapter($filesystem);
    }
    /**
     * Set the given disk instance.
     *
     * @param  string  $name
     * @param  mixed  $disk
     * @return void
     */
    public function set($name, $disk)
    {
        $this->disks[$name] = $disk;
    }
    /**
     * Get the filesystem connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["filesystems.disks.{$name}"];
    }
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['filesystems.default'];
    }
    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudDriver()
    {
        return $this->app['config']['filesystems.cloud'];
    }
    /**
     * Register a custom driver creator Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, \Closure $callback)
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }
    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->disk()->{$method}(...$parameters);
    }
}
