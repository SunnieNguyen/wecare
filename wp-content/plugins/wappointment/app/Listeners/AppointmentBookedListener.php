<?php

namespace Wappointment\Listeners;

use Wappointment\Models\Reminder;
class AppointmentBookedListener extends \Wappointment\Listeners\AppointmentConfirmedListener
{
    protected $jobClass = '\\Wappointment\\Jobs\\AppointmentEmailPending';
    protected $event_trigger = \Wappointment\Models\Reminder::APPOINTMENT_PENDING;
}
