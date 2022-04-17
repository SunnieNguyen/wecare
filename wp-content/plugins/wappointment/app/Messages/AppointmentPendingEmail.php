<?php

namespace Wappointment\Messages;

class AppointmentPendingEmail extends \Wappointment\Messages\ClientBookingConfirmationEmail
{
    use HasNoAppointmentFooterLinks;
    const EVENT = \Wappointment\Models\Reminder::APPOINTMENT_PENDING;
}
