<?php

namespace Wappointment\Controllers;

use Wappointment\Services\ViewsData;
use Wappointment\ClassConnect\Request;
class AvailabilityController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        //return json_decode(file_get_contents(dirname(dirname(dirname(__FILE__))) . '/test_availability.json'));
        return (new \Wappointment\Services\ViewsData())->load('front_availability');
    }
}
