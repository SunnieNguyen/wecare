<?php

namespace Wappointment\ClassConnect;

use Wappointment\Services\VersionDB;
if (\Wappointment\Services\VersionDB::atLeast(\Wappointment\Services\VersionDB::CAN_DEL_CLIENT)) {
    trait ClientSoftDeletes
    {
        use SoftDeletes;
    }
} else {
    trait ClientSoftDeletes
    {
    }
}
