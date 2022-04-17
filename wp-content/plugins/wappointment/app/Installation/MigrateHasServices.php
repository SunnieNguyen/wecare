<?php

namespace Wappointment\Installation;

use Wappointment\Config\Database;
use Wappointment\ClassConnect\Capsule;
use Wappointment\System\Status;
class MigrateHasServices extends \Wappointment\Installation\Migrate
{
    public function hasMultiService()
    {
        return \Wappointment\System\Status::dbVersionAlterRequired() && \Wappointment\ClassConnect\Capsule::schema()->hasTable(\Wappointment\Config\Database::$prefix_self . '_services') && \Wappointment\ClassConnect\Capsule::schema()->hasTable(\Wappointment\Config\Database::$prefix_self . '_locations') && \Wappointment\ClassConnect\Capsule::schema()->hasTable(\Wappointment\Config\Database::$prefix_self . '_service_location') && \defined('WAPPOINTMENT_SERVICES_VERSION');
    }
}
