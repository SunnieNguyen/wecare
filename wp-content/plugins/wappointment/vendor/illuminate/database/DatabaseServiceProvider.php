<?php

namespace WappoVendor\Illuminate\Database;

use WappoVendor\Faker\Factory as FakerFactory;
use WappoVendor\Faker\Generator as FakerGenerator;
use WappoVendor\Illuminate\Database\Eloquent\Model;
use WappoVendor\Illuminate\Support\ServiceProvider;
use WappoVendor\Illuminate\Contracts\Queue\EntityResolver;
use WappoVendor\Illuminate\Database\Connectors\ConnectionFactory;
use WappoVendor\Illuminate\Database\Eloquent\QueueEntityResolver;
use WappoVendor\Illuminate\Database\Eloquent\Factory as EloquentFactory;
class DatabaseServiceProvider extends \WappoVendor\Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        \WappoVendor\Illuminate\Database\Eloquent\Model::setConnectionResolver($this->app['db']);
        \WappoVendor\Illuminate\Database\Eloquent\Model::setEventDispatcher($this->app['events']);
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \WappoVendor\Illuminate\Database\Eloquent\Model::clearBootedModels();
        $this->registerConnectionServices();
        $this->registerEloquentFactory();
        $this->registerQueueableEntityResolver();
    }
    /**
     * Register the primary database bindings.
     *
     * @return void
     */
    protected function registerConnectionServices()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new \WappoVendor\Illuminate\Database\Connectors\ConnectionFactory($app);
        });
        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            return new \WappoVendor\Illuminate\Database\DatabaseManager($app, $app['db.factory']);
        });
        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }
    /**
     * Register the Eloquent factory instance in the container.
     *
     * @return void
     */
    protected function registerEloquentFactory()
    {
        $this->app->singleton(\WappoVendor\Faker\Generator::class, function ($app) {
            return \WappoVendor\Faker\Factory::create($app['config']->get('app.faker_locale', 'en_US'));
        });
        $this->app->singleton(\WappoVendor\Illuminate\Database\Eloquent\Factory::class, function ($app) {
            return \WappoVendor\Illuminate\Database\Eloquent\Factory::construct($app->make(\WappoVendor\Faker\Generator::class), $this->app->databasePath('factories'));
        });
    }
    /**
     * Register the queueable entity resolver implementation.
     *
     * @return void
     */
    protected function registerQueueableEntityResolver()
    {
        $this->app->singleton(\WappoVendor\Illuminate\Contracts\Queue\EntityResolver::class, function () {
            return new \WappoVendor\Illuminate\Database\Eloquent\QueueEntityResolver();
        });
    }
}
