<?php

namespace Wappointment\Listeners;

use Wappointment\Models\Reminder;
class AppointmentConfirmedListener extends \Wappointment\Listeners\AbstractJobAppointmentListener
{
    protected $jobClass = '\\Wappointment\\Jobs\\AppointmentEmailConfirmed';
    protected $delay = 0;
    protected $event_trigger = \Wappointment\Models\Reminder::APPOINTMENT_CONFIRMED;
    protected function addToJobs($event)
    {
        $params = ['appointment' => $event->getAppointment(), 'client' => $event->getClient(), 'order' => $event->getOrder(), 'args' => $event->getAdditional()];
        foreach ($event->getReminders() as $reminder) {
            if ($reminder->type == \Wappointment\Models\Reminder::getType('email') && $reminder->event == $this->event_trigger) {
                $params['reminder_id'] = 0;
                $this->recordJob($this->jobClass, \array_merge($params, $this->data_job), 'client', null, $this->delay);
            }
        }
    }
}
