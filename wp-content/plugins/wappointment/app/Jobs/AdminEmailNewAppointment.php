<?php

namespace Wappointment\Jobs;

class AdminEmailNewAppointment extends \Wappointment\Jobs\AbstractAppointmentEmailJob
{
    use IsAdminAppointmentJob;
    const CONTENT = '\\Wappointment\\Messages\\AdminNewAppointmentEmail';
}
