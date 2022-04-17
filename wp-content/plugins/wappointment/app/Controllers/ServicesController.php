<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Controllers\RestController;
use Wappointment\Helpers\Get;
use Wappointment\Helpers\Translations;
use Wappointment\Services\Services;
use Wappointment\Services\VersionDB;
use Wappointment\Managers\Service;
use Wappointment\Repositories\Services as RepositoriesServices;
use Wappointment\Services\Payment;
use Wappointment\Services\Settings;
class ServicesController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        $serviceModel = \Wappointment\Managers\Service::model();
        $db_update_required = \Wappointment\Services\VersionDB::isLessThan(\Wappointment\Services\VersionDB::CAN_CREATE_SERVICES);
        $services = $db_update_required ? $this->getlegacy() : (new \Wappointment\Repositories\Services())->get();
        $data = ['db_required' => $db_update_required, 'services' => $services, 'currency' => \Wappointment\Services\Payment::currencyCode(), 'tax' => \Wappointment\Services\Settings::get('tax')];
        if (!$db_update_required) {
            $data['limit_reached'] = $serviceModel::canCreate() ? false : \Wappointment\Helpers\Translations::get('add_calendars_addon', [\Wappointment\Helpers\Get::list('addons')['wappointment_services']['name']]);
        }
        return $data;
    }
    public function getlegacy()
    {
        return \Wappointment\Managers\Service::all();
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        $data = $request->only(['id', 'name', 'options', 'locations_id']);
        $new = false;
        if (empty($data['id'])) {
            $data['sorting'] = \Wappointment\Services\Services::total();
            $new = true;
        }
        $result = \Wappointment\Services\Services::save($data);
        if ($new) {
            \Wappointment\Services\Services::reorder($result->id, 0);
        }
        $this->refreshRepository();
        return ['message' => \Wappointment\Helpers\Translations::get('element_saved') . __('Next, assign it to your staff in Wappointment > Settings > Calendars & Staff', 'wappointment'), 'result' => $result];
    }
    protected function refreshRepository()
    {
        (new \Wappointment\Repositories\Services())->refresh();
    }
    public function reorder(\Wappointment\ClassConnect\Request $request)
    {
        $data = $request->only(['id', 'new_sorting']);
        $result = \Wappointment\Services\Services::reorder($data['id'], $data['new_sorting']);
        $this->refreshRepository();
        return ['message' => \Wappointment\Helpers\Translations::get('element_reordered'), 'result' => $result];
    }
    public function delete(\Wappointment\ClassConnect\Request $request)
    {
        \Wappointment\Services\Services::delete($request->input('id'));
        $this->refreshRepository();
        // clean order
        return ['message' => \Wappointment\Helpers\Translations::get('element_deleted'), 'result' => true];
    }
}
