<?php

namespace Wappointment\Listeners;

use Wappointment\Models\Reminder;
class AppointmentReminderListener extends \Wappointment\Listeners\AbstractJobAppointmentListener
{
    use IsReminder;
    protected $jobClass = '\\Wappointment\\Jobs\\AppointmentEmailReminder';
    protected $is_reminder = true;
    protected function addToJobs($event)
    {
        $params = ['appointment' => $event->getAppointment(), 'client' => $event->getClient(), 'args' => $event->getAdditional()];
        if (!empty($event->getClient()['options']) && !empty($event->getClient()['options']['test_appointment'])) {
            return;
        }
        foreach ($event->getReminders() as $reminder) {
            if ($reminder->type == \Wappointment\Models\Reminder::getType('email') && $reminder->event == \Wappointment\Models\Reminder::APPOINTMENT_STARTS) {
                $this->recordReminder($reminder, $event, $params);
            }
        }
    }
}
