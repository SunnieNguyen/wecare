<?php

namespace WappoVendor\Illuminate\Database;

use WappoVendor\Illuminate\Support\ServiceProvider;
use WappoVendor\Illuminate\Database\Migrations\Migrator;
use WappoVendor\Illuminate\Database\Migrations\MigrationCreator;
use WappoVendor\Illuminate\Database\Migrations\DatabaseMigrationRepository;
class MigrationServiceProvider extends \WappoVendor\Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepository();
        $this->registerMigrator();
        $this->registerCreator();
    }
    /**
     * Register the migration repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];
            return new \WappoVendor\Illuminate\Database\Migrations\DatabaseMigrationRepository($app['db'], $table);
        });
    }
    /**
     * Register the migrator service.
     *
     * @return void
     */
    protected function registerMigrator()
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];
            return new \WappoVendor\Illuminate\Database\Migrations\Migrator($repository, $app['db'], $app['files']);
        });
    }
    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new \WappoVendor\Illuminate\Database\Migrations\MigrationCreator($app['files']);
        });
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['migrator', 'migration.repository', 'migration.creator'];
    }
}
