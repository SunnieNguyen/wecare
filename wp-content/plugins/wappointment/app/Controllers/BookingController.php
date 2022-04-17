<?php

namespace Wappointment\Controllers;

use Wappointment\Services\Client;
use Wappointment\Validators\HttpRequest\Booking;
use Wappointment\Validators\HttpRequest\BookingAdmin;
use Wappointment\Services\Admin;
use Wappointment\Services\AppointmentNew as Appointment;
use Wappointment\ClassConnect\Request;
use Wappointment\Formatters\BookingResult;
use Wappointment\WP\Helpers as WPHelpers;
use Wappointment\Helpers\Translations;
use Wappointment\Services\DateTime;
class BookingController extends \Wappointment\Controllers\RestController
{
    protected function fieldsError($booking)
    {
        return \Wappointment\WP\Helpers::restError(\Wappointment\Helpers\Translations::get('review_fields'), 500, $booking->getErrors());
    }
    protected function bookingFailed()
    {
        return __('Booking failed', 'wappointment');
    }
    public function save(\Wappointment\Validators\HttpRequest\Booking $booking)
    {
        if ($booking->hasErrors()) {
            return $this->fieldsError($booking);
        }
        $result = \Wappointment\Services\Client::book($booking);
        if (isset($result['appointment']['errors'])) {
            return \Wappointment\WP\Helpers::restError($this->bookingFailed(), 500, $result['appointment']['errors']);
        }
        $result['result'] = true;
        return \Wappointment\Formatters\BookingResult::format($result);
    }
    public function adminBook(\Wappointment\Validators\HttpRequest\BookingAdmin $booking)
    {
        if ($booking->hasErrors()) {
            return $this->fieldsError($booking);
        }
        $result = \Wappointment\Services\Admin::book($booking);
        if (isset($result['errors'])) {
            return \Wappointment\WP\Helpers::restError($this->bookingFailed(), 500, $result['errors']);
        }
        return ['message' => __('Appointment recorded', 'wappointment')];
    }
    public function reschedule(\Wappointment\ClassConnect\Request $request)
    {
        return \Wappointment\Services\AppointmentNew::reschedule($request->input('appointmentkey'), $request->input('time'));
    }
    public function convertDate(\Wappointment\ClassConnect\Request $request)
    {
        return ['converted' => $this->convert((int) $request->input('timestamp'), $request->input('timezone'))];
    }
    protected function convert($ts, $tz)
    {
        return \Wappointment\Services\DateTime::i18nDateTime($ts, $tz);
    }
}
