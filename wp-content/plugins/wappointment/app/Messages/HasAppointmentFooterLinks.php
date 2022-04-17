<?php

namespace Wappointment\Messages;

use Wappointment\Services\Settings;
trait HasAppointmentFooterLinks
{
    protected $separator = ' | ';
    private function calendarLink()
    {
        return '<a href="[appointment:linkAddEventToCalendar]">' . \Wappointment\Services\Settings::get('save_appointment_text_link') . '</a>';
    }
    private function rescheduleAndCancelLinks()
    {
        $links = '';
        if (apply_filters('wappointment_reschedule_allowed', \Wappointment\Services\Settings::get('allow_rescheduling'), $this->params)) {
            $links .= '<a href="[appointment:linkRescheduleEvent]">' . \Wappointment\Services\Settings::get('reschedule_link') . '</a>';
        }
        if (\Wappointment\Services\Settings::get('allow_cancellation')) {
            if (!empty($links)) {
                $links .= $this->separator;
            }
            $links .= '<a href="[appointment:linkCancelEvent]">' . \Wappointment\Services\Settings::get('cancellation_link') . '</a></p>';
        }
        return empty($links) ? '' : $links;
    }
    protected function footerLinks()
    {
        $rescheduleAndCancelLinks = $this->rescheduleAndCancelLinks();
        if (!empty($rescheduleAndCancelLinks)) {
            $rescheduleAndCancelLinks = $this->separator . $rescheduleAndCancelLinks;
        }
        return '<p>' . $this->calendarLink() . $rescheduleAndCancelLinks . '</p>';
    }
}
