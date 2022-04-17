<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Controllers\RestController;
use Wappointment\Helpers\Translations;
use Wappointment\Models\Location;
use Wappointment\Services\Location as LocationService;
class LocationsController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        $locations = \Wappointment\Models\Location::get();
        if ($request->input('usable')) {
            $locations = $locations->filter(function ($value) {
                if ($value->type == \Wappointment\Models\Location::TYPE_AT_LOCATION && empty($value->options['address'])) {
                    return false;
                }
                if ($value->type == \Wappointment\Models\Location::TYPE_PHONE && empty($value->options['countries'])) {
                    return false;
                }
                if ($value->type == \Wappointment\Models\Location::TYPE_ZOOM && empty($value->options['video'])) {
                    return false;
                }
                return true;
            });
        }
        return \array_values($locations->toArray());
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        $result = \Wappointment\Services\Location::save($request->only(['id', 'name', 'type', 'options']));
        return ['message' => \Wappointment\Helpers\Translations::get('element_saved'), 'result' => $result, 'locations' => $this->get($request)];
    }
    public function delete(\Wappointment\ClassConnect\Request $request)
    {
        if ((int) $request->input('id') < 5) {
            throw new \WappointmentException(\Wappointment\Helpers\Translations::get('error_deleting'), 1);
        }
        return ['message' => \Wappointment\Helpers\Translations::get('element_deleted'), 'result' => \Wappointment\Models\Location::destroy($request->input('id')), 'deleted' => $request->input('id')];
    }
}
