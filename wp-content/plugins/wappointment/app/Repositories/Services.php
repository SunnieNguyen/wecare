<?php

namespace Wappointment\Repositories;

use Wappointment\Managers\Central;
use Wappointment\Services\Settings;
class Services extends \Wappointment\Repositories\AbstractRepository
{
    use MustRefreshAvailability;
    public $cache_key = 'services';
    public function query()
    {
        $result = \Wappointment\Managers\Central::get('ServiceModel')::orderBy('sorting')->fetch();
        $this->testIfsold($result);
        $this->refreshAvailability();
        return $result->toArray();
    }
    protected function testIfsold($services)
    {
        foreach ($services as $service) {
            if (!empty($service->options['woo_sellable'])) {
                \Wappointment\Services\Settings::save('services_sold', true);
                return;
            }
        }
    }
}
