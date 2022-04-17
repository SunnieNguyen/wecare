<?php

namespace WappoVendor\Illuminate\Pagination;

use WappoVendor\Illuminate\Support\ServiceProvider;
class PaginationServiceProvider extends \WappoVendor\Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'pagination');
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/resources/views' => $this->app->resourcePath('views/vendor/pagination')], 'laravel-pagination');
        }
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        \WappoVendor\Illuminate\Pagination\Paginator::viewFactoryResolver(function () {
            return $this->app['view'];
        });
        \WappoVendor\Illuminate\Pagination\Paginator::currentPathResolver(function () {
            return $this->app['request']->url();
        });
        \WappoVendor\Illuminate\Pagination\Paginator::currentPageResolver(function ($pageName = 'page') {
            $page = $this->app['request']->input($pageName);
            if (\filter_var($page, \FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }
            return 1;
        });
    }
}
