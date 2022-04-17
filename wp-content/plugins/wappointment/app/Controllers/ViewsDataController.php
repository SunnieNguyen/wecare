<?php

namespace Wappointment\Controllers;

use Wappointment\Services\ViewsData;
use Wappointment\ClassConnect\Request;
class ViewsDataController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        return (new \Wappointment\Services\ViewsData())->load($request->input('key'));
    }
    public function getCalendar()
    {
        return (new \Wappointment\Services\ViewsData())->load('calendar');
    }
}
