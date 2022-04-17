<?php

namespace Wappointment\Jobs;

class AdminEmailPendingAppointment extends \Wappointment\Jobs\AbstractAppointmentEmailJob
{
    use IsAdminAppointmentJob;
    const CONTENT = '\\Wappointment\\Messages\\AdminPendingAppointmentEmail';
}
