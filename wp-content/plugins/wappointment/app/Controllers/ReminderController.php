<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Helpers\Translations;
use Wappointment\Services\Reminder;
use Wappointment\Models\Reminder as MReminder;
use Wappointment\Services\Settings;
use Wappointment\Services\VersionDB;
class ReminderController extends \Wappointment\Controllers\RestController
{
    private $columns = ['id', 'subject', 'type', 'event', 'locked', 'published', 'options'];
    public function isLegacy()
    {
        return !\Wappointment\Services\VersionDB::canServices();
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        $requested = $request->except(['rest_route', 'locked', 'email_logo', 'label']);
        $requested['published'] = true;
        $this->saveImage($request);
        if ($this->isTrueOrFail(\Wappointment\Services\Reminder::save($requested))) {
            return ['message' => \Wappointment\Helpers\Translations::get('element_saved')];
        }
        throw new \WappointmentException(\Wappointment\Helpers\Translations::get('error_saving'), 1);
    }
    protected function saveImage(\Wappointment\ClassConnect\Request $request)
    {
        if ($request->has('email_logo')) {
            if ($this->isLegacy()) {
                \Wappointment\Services\Settings::saveStaff('email_logo', $request->input('email_logo'));
            } else {
                \Wappointment\Services\Settings::save('email_logo', $request->input('email_logo'));
            }
        }
    }
    public function patch(\Wappointment\ClassConnect\Request $request)
    {
        $this->saveImage($request);
        if ($this->isTrueOrFail(\Wappointment\Services\Reminder::save($request->except(['rest_route', 'locked', 'email_logo', 'label'])))) {
            return ['message' => \Wappointment\Helpers\Translations::get('element_updated')];
        }
        throw new \WappointmentException(\Wappointment\Helpers\Translations::get('error_updating'), 1);
    }
    public function preview(\Wappointment\ClassConnect\Request $request)
    {
        if ($this->isTrueOrFail(\Wappointment\Services\Reminder::preview($request->input('reminder'), $request->input('recipient')))) {
            return ['message' => __('Reminder preview sent', 'wappointment')];
        }
        throw new \WappointmentException(__('Error sending', 'wappointment'), 1);
    }
    public function delete(\Wappointment\ClassConnect\Request $request)
    {
        if (\Wappointment\Services\Reminder::delete($request->input('id'))) {
            return ['message' => \Wappointment\Helpers\Translations::get('element_deleted')];
        }
        throw new \WappointmentException(\Wappointment\Helpers\Translations::get('error_deleting'), 1);
    }
    public function get()
    {
        $queryReminders = \Wappointment\Models\Reminder::select($this->columns);
        $queryReminders->activeReminders();
        $queryReminders->whereIn('type', \Wappointment\Models\Reminder::getTypes('code'));
        $data = ['mail_status' => (bool) \Wappointment\Services\Settings::get('mail_status'), 'allow_cancellation' => (bool) \Wappointment\Services\Settings::get('allow_cancellation'), 'allow_rescheduling' => (bool) \Wappointment\Services\Settings::get('allow_rescheduling'), 'reschedule_link' => \Wappointment\Services\Settings::get('reschedule_link'), 'cancellation_link' => \Wappointment\Services\Settings::get('cancellation_link'), 'save_appointment_text_link' => \Wappointment\Services\Settings::get('save_appointment_text_link'), 'multiple_service_type' => \Wappointment\Helpers\Service::hasMultipleTypes($this->isLegacy()), 'reminders' => $queryReminders->get(), 'recipient' => wp_get_current_user()->user_email, 'defaultReminders' => ['email' => \Wappointment\Services\Reminder::getSeedReminder()], 'labels' => ['types' => \Wappointment\Models\Reminder::getTypes(), 'events' => \Wappointment\Models\Reminder::getEvents()]];
        $data['email_logo'] = $this->isLegacy() ? \Wappointment\Services\Settings::getStaff('email_logo') : \Wappointment\Services\Settings::get('email_logo');
        return apply_filters('wappointment_settings_reminders_get', $data);
    }
}
