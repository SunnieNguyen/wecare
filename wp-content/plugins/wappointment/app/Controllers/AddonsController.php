<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Services\Wappointment\Licences;
use Wappointment\Services\Wappointment\Addons;
use Wappointment\Services\Settings;
use Wappointment\WP\Helpers as WPHelpers;
class AddonsController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        if ($request->input('remember') == 'true') {
            \Wappointment\Services\Settings::save('wappointment_allowed', true);
        }
        $data = (new \Wappointment\Services\Wappointment\Addons())->get();
        $data->admin_email = \Wappointment\Services\Settings::get('email_notifications')[0];
        $statuses = \Wappointment\WP\Helpers::getOption('subscribed_status');
        $data->statuses = $statuses === false ? [] : $statuses;
        $data->wappointment_allowed = \Wappointment\Services\Settings::get('wappointment_allowed');
        $data->has_addon = !empty(\Wappointment\WP\Helpers::getOption('site_details'));
        $data->site_key = \Wappointment\WP\Helpers::getOption('site_key');
        return $data;
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        $result = (new \Wappointment\Services\Wappointment\Licences())->register($request->input('pkey'));
        return ['message' => $result->message, 'addons' => (new \Wappointment\Services\Wappointment\Addons())->get()->addons];
    }
    public function install(\Wappointment\ClassConnect\Request $request)
    {
        return (new \Wappointment\Services\Wappointment\Addons())->install((object) $request->input('addon'));
    }
    public function activate(\Wappointment\ClassConnect\Request $request)
    {
        return (new \Wappointment\Services\Wappointment\Addons())->activate((object) $request->input('addon'));
    }
    public function deactivate(\Wappointment\ClassConnect\Request $request)
    {
        return (new \Wappointment\Services\Wappointment\Addons())->deactivate((object) $request->input('addon'));
    }
    public function check()
    {
        $resultCheck = (new \Wappointment\Services\Wappointment\Licences())->check();
        if ($resultCheck) {
            return ['message' => 'Success checking licence'];
        }
    }
    public function clear()
    {
        $resultCheck = (new \Wappointment\Services\Wappointment\Licences())->clear();
        if ($resultCheck) {
            return ['message' => 'Success clearing licence'];
        }
    }
}
