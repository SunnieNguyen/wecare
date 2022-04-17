<?php

namespace Wappointment\Services;

use Wappointment\Models\Client as MClient;
use Wappointment\Validators\HttpRequest\BookingAdmin;
class Admin
{
    public static function book(\Wappointment\Validators\HttpRequest\BookingAdmin $booking)
    {
        $client_id = $booking->get('clientid');
        if ($client_id > 0) {
            $client = \Wappointment\Models\Client::find((int) $client_id);
        } else {
            $client = \Wappointment\Models\Client::where('email', $booking->get('email'))->withTrashed()->first();
            if ($client->trashed()) {
                $client->restore();
            }
        }
        if (empty($client)) {
            $dataClient = $booking->preparedData();
            $client = \Wappointment\Models\Client::create($dataClient);
        }
        //book with that client
        return $client->bookAsAdmin($booking);
    }
}
