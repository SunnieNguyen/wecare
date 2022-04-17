<?php

namespace Wappointment\Controllers;

use Wappointment\Services\Settings;
use Wappointment\ClassConnect\Request;
use Wappointment\System\Status;
use Wappointment\Services\Calendars;
class SettingsStaffController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        return \Wappointment\Services\Settings::getStaff($request->input('key'));
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        if ($request->input('key') == 'viewed_updates') {
            return \Wappointment\System\Status::setViewedUpdated();
        }
        $value = $request->input('val');
        if ($request->input('key') == 'regav') {
            //legacy
            $value = \Wappointment\Services\Calendars::regavClean($value);
            //clean invalid entry in regav
        }
        $result = \Wappointment\Services\Settings::saveStaff($request->input('key'), $value);
        //TODO Legacy remove at some point
        if (\in_array($request->input('key'), ['regav', 'availaible_booking_days'])) {
            (new \Wappointment\Services\Availability())->regenerate();
            //legacy
        }
        return $result;
    }
}
