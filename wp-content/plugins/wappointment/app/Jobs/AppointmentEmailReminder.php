<?php

namespace Wappointment\Jobs;

class AppointmentEmailReminder extends \Wappointment\Jobs\AppointmentEmailConfirmed
{
    const CONTENT = '\\Wappointment\\Messages\\AppointmentReminderEmail';
}
