<?php

namespace Wappointment\Repositories;

use Wappointment\Managers\Central;
use Wappointment\Services\Staff;
use Wappointment\Managers\Service as ManageService;
use Wappointment\Services\Settings;
use Wappointment\ClassConnect\Carbon;
class Availability extends \Wappointment\Repositories\AbstractRepository
{
    public $cache_key = 'availability';
    public function query()
    {
        return apply_filters('wappointment_front_availability', ['staffs' => \Wappointment\Services\Staff::get(), 'week_starts_on' => \Wappointment\Services\Settings::get('week_starts_on'), 'frontend_weekstart' => \Wappointment\Services\Settings::get('frontend_weekstart'), 'date_format' => \Wappointment\Services\Settings::get('date_format'), 'time_format' => \Wappointment\Services\Settings::get('time_format'), 'min_bookable' => \Wappointment\Services\Settings::get('hours_before_booking_allowed'), 'date_time_union' => \Wappointment\Services\Settings::get('date_time_union', ' - '), 'now' => (new \Wappointment\ClassConnect\Carbon())->format('Y-m-d\\TH:i:00'), 'buffer_time' => \Wappointment\Services\Settings::get('buffer_time'), 'services' => \Wappointment\Managers\Service::all(), 'site_lang' => \substr(get_locale(), 0, 2), 'custom_fields' => \Wappointment\Managers\Central::get('CustomFields')::get(), 'availability_fluid' => \Wappointment\Services\Settings::get('availability_fluid')]);
    }
}
