<?php

namespace Wappointment\Controllers;

use Wappointment\Services\Permissions;
use Wappointment\Services\Reset;
use Wappointment\Services\ViewsData;
class DebugController extends \Wappointment\Controllers\RestController
{
    public function freshInstall()
    {
        (new \Wappointment\Services\Reset())->proceed();
        return ['message' => 'Plugin has been fully reseted.'];
    }
    public function updatePage()
    {
        (new \Wappointment\WP\CustomPage())->makeEditablePage();
        return (new \Wappointment\Services\ViewsData())->load('settingsadvanced');
    }
    public function refreshCache()
    {
        \Wappointment\Services\Reset::refreshCache();
        return ['message' => __('Cache has been reseted', 'wappointment')];
    }
    public function addManagerRole()
    {
        $perms = new \Wappointment\Services\Permissions();
        $perms->registerRole('wappointment_manager');
        return (new \Wappointment\Services\ViewsData())->load('settingsadvanced');
    }
}
