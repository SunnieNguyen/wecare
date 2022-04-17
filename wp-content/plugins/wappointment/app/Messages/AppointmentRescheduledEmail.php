<?php

namespace Wappointment\Messages;

use Wappointment\Models\Reminder;
class AppointmentRescheduledEmail extends \Wappointment\Messages\AbstractEmail
{
    use HasAppointmentFooterLinks, HasTagsToReplace, AttachesIcs, PreparesClientEmail;
    protected $client = null;
    protected $appointment = null;
    protected $icsRequired = true;
    const EVENT = \Wappointment\Models\Reminder::APPOINTMENT_RESCHEDULED;
    public function loadContent()
    {
        if (!$this->prepareClientEmail($this->params['client'], $this->params['appointment'], static::EVENT)) {
            return false;
        }
        if ($this->icsRequired) {
            $this->attachIcs([$this->params['appointment']], 'appointment');
        }
    }
}
