<?php

namespace Wappointment\Controllers;

use Wappointment\Models\Appointment as AppointmentModel;
use Wappointment\ClassConnect\Request;
use Wappointment\Services\Settings;
use Wappointment\Services\AppointmentNew;
use Wappointment\Services\Service;
use Wappointment\Services\VersionDB;
use Wappointment\Managers\Central;
class AppointmentController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        if (\is_array($request->input('appointmentkey'))) {
            throw new \WappointmentException("Malformed parameter", 1);
        }
        $appointment = \Wappointment\Models\Appointment::select(['id', 'start_at', 'edit_key', 'status', 'end_at', 'type', 'client_id', 'options', 'staff_id', 'service_id', 'location_id'])->where('status', '>=', \Wappointment\Models\Appointment::STATUS_AWAITING_CONFIRMATION)->where('edit_key', $request->input('appointmentkey'))->first();
        if (empty($appointment)) {
            throw new \WappointmentException(__('Can\'t find appointment', 'wappointment'), 1);
        }
        $isLegacy = !\Wappointment\Services\VersionDB::atLeast(\Wappointment\Services\VersionDB::CAN_CREATE_SERVICES);
        $service = $isLegacy ? \Wappointment\Services\Service::get() : \Wappointment\Managers\Central::get('ServiceModel')::find((int) $appointment->service_id);
        $client = $appointment->client()->select(['name', 'email', 'options'])->first();
        return apply_filters('wappointment_appointment_load', ['appointment' => $appointment->toArraySpecial(), 'client' => $client, 'service' => $service, 'staff' => $isLegacy ? (new \Wappointment\WP\StaffLegacy($appointment->getStaffId()))->toArray() : (new \Wappointment\WP\Staff($appointment->getStaffId()))->toArray(), 'date_format' => \Wappointment\Services\Settings::get('date_format'), 'time_format' => \Wappointment\Services\Settings::get('time_format'), 'date_time_union' => \Wappointment\Services\Settings::get('date_time_union', ' - '), 'zoom_browser' => \Wappointment\Services\Settings::get('zoom_browser'), 'display' => [
            '[h2]getText(title)[/h2]',
            /* translators: %1$s is service name, %2$s is the duration  */
            empty($client) ? '' : \sprintf(__('%1$s - %2$s', 'wappointment'), '[b]' . $client->name . '[/b]', $client->email),
            \sprintf(__('%1$s - %2$s', 'wappointment'), '[b]' . $service->name . '[/b]', $appointment->getDuration()),
        ]], $appointment, $request);
    }
    public function cancel(\Wappointment\ClassConnect\Request $request)
    {
        $result = \Wappointment\Services\AppointmentNew::tryCancel($request);
        if ($result) {
            return ['message' => __('Appointment has been canceled', 'wappointment')];
        }
        throw new \WappointmentException("Error Cancelling appointment", 1);
    }
}
