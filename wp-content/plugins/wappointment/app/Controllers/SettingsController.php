<?php

namespace Wappointment\Controllers;

use Wappointment\Services\Settings;
use Wappointment\Services\WidgetSettings;
use Wappointment\Services\TestMail;
use Wappointment\ClassConnect\Request;
use Wappointment\Helpers\Translations;
class SettingsController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        return \Wappointment\Services\Settings::get($request->input('key'));
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        if ($request->input('settings')) {
            foreach ($request->input('settings') as $key => $value) {
                $msg = \Wappointment\Services\Settings::save($key, $value);
            }
            return $msg;
        } else {
            if ($request->input('key') == 'widget') {
                (new \Wappointment\Services\WidgetSettings())->save($request->input('val'));
                return ['message' => \Wappointment\Helpers\Translations::get('element_saved')];
            } else {
                \Wappointment\Services\Settings::save($request->input('key'), $request->input('val'));
                $data = ['message' => \Wappointment\Helpers\Translations::get('element_saved')];
                if ($request->input('key') == 'payments_order') {
                    $data['methods'] = \Wappointment\Services\Payment::methods();
                }
                return $data;
            }
        }
    }
    public function sendPreviewEmail(\Wappointment\ClassConnect\Request $request)
    {
        $resultEmail = \Wappointment\Services\TestMail::send($request->input('data'), $request->input('recipient'));
        if ($this->isTrueOrFail($resultEmail)) {
            \Wappointment\Services\Settings::save('mail_config', $request->input('data'));
            \Wappointment\Services\Settings::save('mail_status', true);
            return ['message' => __('Configuration completed!', 'wappointment') . ' ' . __('Check your inbox for the test email just sent to your address.', 'wappointment')];
        } else {
            if (\Wappointment\ClassConnect\Str::contains($resultEmail['error'], ['username', 'password', 'login', 'user', 'credentials'])) {
                $this->setError(__('Error with your credentials', 'wappointment'));
                $this->setError($resultEmail['error'], 'debug');
            } else {
                $this->setError(__('Couldn\'t send test email.', 'wappointment'));
                $this->setError($resultEmail['error'], 'debug');
            }
        }
    }
}
