<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Helpers\Translations;
use Wappointment\Services\Status;
class StatusController extends \Wappointment\Controllers\RestController
{
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        if ($request->input('type') == 'free') {
            if (\Wappointment\Services\Status::free($request->input('start'), $request->input('end'), $request->input('timezone'), $request, $request->input('staff_id'))) {
                return ['message' => __('Extra free time added', 'wappointment')];
            }
        } elseif ($request->input('type') == 'busy') {
            if (\Wappointment\Services\Status::busy($request->input('start'), $request->input('end'), $request->input('timezone'), $request->input('staff_id'))) {
                return ['message' => __('Busy time added', 'wappointment')];
            }
        }
        throw new \WappointmentException(\Wappointment\Helpers\Translations::get('error_creating'), 1);
    }
    public function delete(\Wappointment\ClassConnect\Request $request)
    {
        $event = \Wappointment\Services\Status::delete($request->input('id'));
        if ($event) {
            return ['message' => !empty($event->source) ? __('Muted successfully', 'wappointment') : \Wappointment\Helpers\Translations::get('element_deleted')];
        }
        throw new \WappointmentException(\Wappointment\Helpers\Translations::get('error_deleting'), 1);
    }
}
