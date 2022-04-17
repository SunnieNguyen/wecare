<?php

namespace Wappointment\Models\Appointment;

use Wappointment\Services\Settings;
use Wappointment\ClassConnect\Carbon;
trait ManipulateCancelReschedule
{
    public function getCanRescheduleUntilAttribute()
    {
        if (\Wappointment\Services\Settings::get('allow_rescheduling')) {
            return $this->canRescheduleUntilTimestamp();
        }
    }
    public function getCanCancelUntilAttribute()
    {
        if (\Wappointment\Services\Settings::get('allow_cancellation')) {
            return $this->canCancelUntilTimestamp();
        }
    }
    public function canRescheduleUntilTimestamp()
    {
        return $this->start_at->getTimestamp() - (float) \Wappointment\Services\Settings::get('hours_before_rescheduling_allowed') * 60 * 60;
    }
    public function canCancelUntilTimestamp()
    {
        return $this->start_at->getTimestamp() - (float) \Wappointment\Services\Settings::get('hours_before_cancellation_allowed') * 60 * 60;
    }
    public function canStillReschedule()
    {
        return $this->canRescheduleUntilTimestamp() - \time() > 0;
    }
    public function canStillCancel()
    {
        return !$this->isConfirmed() || $this->canCancelUntilTimestamp() - \time() > 0;
    }
    public function cancelLimit()
    {
        return \Wappointment\ClassConnect\Carbon::createFromTimestamp($this->canCancelUntilTimestamp())->setTimezone($this->getClientModel()->getTimezone($this->getStaffTZ()))->format($this->longFormat());
    }
    public function rescheduleLimit()
    {
        return \Wappointment\ClassConnect\Carbon::createFromTimestamp($this->canRescheduleUntilTimestamp())->setTimezone($this->getClientModel()->getTimezone($this->getStaffTZ()))->format($this->longFormat());
    }
    protected function longFormat()
    {
        return \Wappointment\Services\Settings::get('date_format') . \Wappointment\Services\Settings::get('date_time_union') . \Wappointment\Services\Settings::get('time_format');
    }
}
