<?php

namespace Wappointment\Controllers;

use Wappointment\Services\Service;
use Wappointment\ClassConnect\Request;
use Wappointment\Services\VersionDB;
use Wappointment\Services\Services;
use Wappointment\Models\Calendar;
class ServiceController extends \Wappointment\Controllers\RestController
{
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        if (\Wappointment\Services\VersionDB::canServices()) {
            $optionsTemp = $request->input('options');
            $locationsIds = \Wappointment\Services\Service::updateLocations($request->input('type'), $optionsTemp, $request->input('address'));
            $options = ['durations' => [['duration' => $request->input('duration')]]];
            if (!empty($optionsTemp['phone_required'])) {
                $options['fields'] = ['name', 'email', 'phone'];
                $options['countries'] = $optionsTemp['countries'];
            }
            $dataService = ['name' => $request->input('name'), 'options' => $options, 'locations_id' => $locationsIds->toArray()];
            if (!empty($request->input('id'))) {
                $dataService['id'] = $request->input('id');
            }
            $serviceDB = \Wappointment\Services\Services::save($dataService);
            $calendar = \Wappointment\Models\Calendar::first();
            // add first service to existing calendar
            $calendar->services()->sync([$serviceDB->id]);
            return true;
        } else {
            return $this->isTrueOrFail(\Wappointment\Services\Service::save($request->only(['name', 'duration', 'type', 'address', 'options'])));
        }
    }
}
