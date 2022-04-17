<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Filesystem\Filesystem;
/**
 * @see \Illuminate\Filesystem\FilesystemManager
 */
class Storage extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Replace the given disk with a local testing disk.
     *
     * @param  string|null  $disk
     *
     * @return void
     */
    public static function fake($disk = null)
    {
        $disk = $disk ?: self::$app['config']->get('filesystems.default');
        (new \WappoVendor\Illuminate\Filesystem\Filesystem())->cleanDirectory($root = storage_path('framework/testing/disks/' . $disk));
        static::set($disk, self::createLocalDriver(['root' => $root]));
    }
    /**
     * Replace the given disk with a persistent local testing disk.
     *
     * @param  string|null  $disk
     * @return void
     */
    public static function persistentFake($disk = null)
    {
        $disk = $disk ?: self::$app['config']->get('filesystems.default');
        static::set($disk, self::createLocalDriver(['root' => storage_path('framework/testing/disks/' . $disk)]));
    }
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filesystem';
    }
}
