<?php

namespace Wappointment\Messages;

class AppointmentCanceledEmail extends \Wappointment\Messages\ClientBookingConfirmationEmail
{
    use HasNoAppointmentFooterLinks;
    protected $icsRequired = false;
    const EVENT = \Wappointment\Models\Reminder::APPOINTMENT_CANCELLED;
}
