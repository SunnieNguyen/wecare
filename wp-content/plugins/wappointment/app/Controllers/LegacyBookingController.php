<?php

namespace Wappointment\Controllers;

use Wappointment\Helpers\Translations;
use Wappointment\Services\ClientLegacy;
use Wappointment\Services\Settings;
use Wappointment\Validators\HttpRequest\LegacyBooking;
use Wappointment\WP\Helpers as WPHelpers;
class LegacyBookingController extends \Wappointment\Controllers\RestController
{
    public function save(\Wappointment\Validators\HttpRequest\LegacyBooking $booking)
    {
        if ($booking->hasErrors()) {
            return \Wappointment\WP\Helpers::restError(\Wappointment\Helpers\Translations::get('review_fields'), 500, $booking->getErrors());
        }
        $appointment = \Wappointment\Services\ClientLegacy::book($booking);
        if (isset($appointment['errors'])) {
            return \Wappointment\WP\Helpers::restError(\Wappointment\Helpers\Translations::get('booking_failed'), 500, $appointment['errors']);
        }
        $appointmentArray = $appointment->toArraySpecial();
        return ['appointment' => (new \Wappointment\ClassConnect\Collection($appointmentArray))->except(['rest_route', 'id', 'client_id'])];
    }
}
