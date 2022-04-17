<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Helpers\Translations;
use Wappointment\Models\Appointment;
use Wappointment\Services\Client;
use Wappointment\Validators\HttpRequest\BookingAdmin;
use Wappointment\Services\Admin;
use Wappointment\WP\Helpers as WPHelpers;
use Wappointment\Models\Client as ClientModel;
use Wappointment\Services\CurrentUser;
use Wappointment\Services\DateTime;
use Wappointment\Services\Settings;
class ClientController extends \Wappointment\Controllers\RestController
{
    public function search(\Wappointment\ClassConnect\Request $request)
    {
        return \Wappointment\Services\Client::search($request->input('email'));
    }
    public function book(\Wappointment\Validators\HttpRequest\BookingAdmin $booking)
    {
        if ($booking->hasErrors()) {
            return \Wappointment\WP\Helpers::restError(\Wappointment\Helpers\Translations::get('review_fields'), 500, $booking->getErrors());
        }
        $result = \Wappointment\Services\Admin::book($booking);
        if (isset($result['errors'])) {
            return \Wappointment\WP\Helpers::restError(\Wappointment\Helpers\Translations::get('booking_failed'), 500, $result['errors']);
        }
        return ['message' => __('Appointment recorded', 'wappointment')];
    }
    public function index(\Wappointment\ClassConnect\Request $request)
    {
        if (!empty($request->input('per_page'))) {
            \Wappointment\Services\Settings::saveStaff('per_page', $request->input('per_page'));
        }
        return ['page' => $request->input('page'), 'viewData' => ['per_page' => \Wappointment\Services\Settings::getStaff('per_page'), 'timezones_list' => \Wappointment\Services\DateTime::tz()], 'clients' => $this->getClients()];
    }
    protected function getClients()
    {
        $query = \Wappointment\Models\Client::orderBy('id', 'DESC');
        if (!\Wappointment\Services\CurrentUser::isAdmin()) {
            $raw = \str_replace('?', \Wappointment\Services\CurrentUser::calendarId(), \Wappointment\Models\Appointment::select('client_id')->where('staff_id', \Wappointment\Services\CurrentUser::calendarId())->distinct()->toSql());
            $query->whereRaw('id IN (' . $raw . ')');
        }
        return $query->paginate(\Wappointment\Services\Settings::getStaff('per_page'));
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        $this->testIsClientOwned($request);
        \Wappointment\Services\Client::save($request->all());
        return ['message' => \Wappointment\Helpers\Translations::get('element_saved')];
    }
    public function delete(\Wappointment\ClassConnect\Request $request)
    {
        $this->testIsClientOwned($request);
        \Wappointment\Models\Client::where('id', (int) $request->input('id'))->delete();
        return ['elementDeleted' => $request->input('id'), 'message' => \Wappointment\Helpers\Translations::get('element_deleted')];
    }
    protected function testIsClientOwned(\Wappointment\ClassConnect\Request $request)
    {
        if (!\Wappointment\Services\CurrentUser::isAdmin()) {
            $appointment = \Wappointment\Models\Appointment::where('client_id', (int) $request->input('id'))->where('staff_id', \Wappointment\Services\CurrentUser::calendarId())->first();
            if (empty($appointment)) {
                throw new \WappointmentException(__('Cannot modify clients that are not yours', 'wappointment'), 1);
            }
        }
    }
}
