<?php

namespace WappoVendor\Carbon\Laravel;

use WappoVendor\Carbon\Carbon;
use WappoVendor\Illuminate\Events\Dispatcher;
use WappoVendor\Illuminate\Events\EventDispatcher;
use WappoVendor\Illuminate\Translation\Translator as IlluminateTranslator;
use WappoVendor\Symfony\Component\Translation\Translator;
class ServiceProvider extends \WappoVendor\Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $service = $this;
        $events = $this->app['events'];
        if ($events instanceof \WappoVendor\Illuminate\Events\EventDispatcher || $events instanceof \WappoVendor\Illuminate\Events\Dispatcher) {
            $events->listen(\class_exists('WappoVendor\\Illuminate\\Foundation\\Events\\LocaleUpdated') ? 'Illuminate\\Foundation\\Events\\LocaleUpdated' : 'locale.changed', function () use($service) {
                $service->updateLocale();
            });
            $service->updateLocale();
        }
    }
    public function updateLocale()
    {
        $translator = $this->app['translator'];
        if ($translator instanceof \WappoVendor\Symfony\Component\Translation\Translator || $translator instanceof \WappoVendor\Illuminate\Translation\Translator) {
            \WappoVendor\Carbon\Carbon::setLocale($translator->getLocale());
        }
    }
    public function register()
    {
        // Needed for Laravel < 5.3 compatibility
    }
}
