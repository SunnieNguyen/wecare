<?php

namespace Wappointment\Jobs;

class AdminEmailCanceledAppointment extends \Wappointment\Jobs\AbstractAppointmentEmailJob
{
    use IsAdminAppointmentJob;
    const CONTENT = '\\Wappointment\\Messages\\AdminCanceledAppointmentEmail';
}
