<?php

namespace Wappointment\System;

use Wappointment\Helpers\Events;
use Wappointment\Services\Settings;
class Listeners
{
    public static function init()
    {
        if (\Wappointment\Services\Settings::get('mail_status')) {
            //pending confirmation event
            \Wappointment\Helpers\Events::listens('appointment.booked', 'AppointmentBookedListener');
            if (\Wappointment\Services\Settings::get('notify_pending_appointments')) {
                \Wappointment\Helpers\Events::listens('appointment.booked', 'AppointmentAdminPendingListener');
            }
            //confirmed event
            \Wappointment\Helpers\Events::listens('appointment.confirmed', 'AppointmentConfirmedListener');
            \Wappointment\Helpers\Events::listens('appointment.confirmed', 'AppointmentReminderListener');
            if (\Wappointment\Services\Settings::get('notify_new_appointments')) {
                \Wappointment\Helpers\Events::listens('appointment.confirmed', 'AdminNotifyNewListener');
            }
            //rescheduled event
            \Wappointment\Helpers\Events::listens('appointment.rescheduled', 'AppointmentRescheduledListener');
            if (\Wappointment\Services\Settings::get('notify_rescheduled_appointments')) {
                \Wappointment\Helpers\Events::listens('appointment.rescheduled', 'AdminNotifyRescheduledListener');
            }
            //canceled event
            \Wappointment\Helpers\Events::listens('appointment.canceled', 'AppointmentCanceledListener');
            if (\Wappointment\Services\Settings::get('notify_canceled_appointments')) {
                \Wappointment\Helpers\Events::listens('appointment.canceled', 'AdminNotifyCanceledListener');
            }
        }
        do_action('wappointments_listeners_init');
    }
}
