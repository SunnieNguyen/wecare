<?php

namespace Wappointment\Jobs;

use Wappointment\Models\Appointment;
class AdminEmailRescheduledAppointment extends \Wappointment\Jobs\AbstractAppointmentEmailJob
{
    use IsAdminAppointmentJob;
    const CONTENT = '\\Wappointment\\Messages\\AdminRescheduledAppointmentEmail';
    protected function generateContent()
    {
        $emailClass = static::CONTENT;
        $data = ['client' => $this->client, 'appointment' => $this->appointment, 'oldAppointment' => new \Wappointment\Models\Appointment($this->params['oldAppointment'])];
        return new $emailClass($data);
    }
}
