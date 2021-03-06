<?php

namespace WappoVendor\Illuminate\Session;

use WappoVendor\Illuminate\Support\ServiceProvider;
use WappoVendor\Illuminate\Session\Middleware\StartSession;
class SessionServiceProvider extends \WappoVendor\Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSessionManager();
        $this->registerSessionDriver();
        $this->app->singleton(\WappoVendor\Illuminate\Session\Middleware\StartSession::class);
    }
    /**
     * Register the session manager instance.
     *
     * @return void
     */
    protected function registerSessionManager()
    {
        $this->app->singleton('session', function ($app) {
            return new \WappoVendor\Illuminate\Session\SessionManager($app);
        });
    }
    /**
     * Register the session driver instance.
     *
     * @return void
     */
    protected function registerSessionDriver()
    {
        $this->app->singleton('session.store', function ($app) {
            // First, we will create the session manager which is responsible for the
            // creation of the various session drivers when they are needed by the
            // application instance, and will resolve them on a lazy load basis.
            return $app->make('session')->driver();
        });
    }
}
