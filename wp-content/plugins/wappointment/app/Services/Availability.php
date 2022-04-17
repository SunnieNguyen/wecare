<?php

namespace Wappointment\Services;

use Wappointment\ClassConnect\Carbon;
use Wappointment\Models\Status as MStatus;
use Wappointment\Models\Appointment;
use Wappointment\WP\Helpers as WPHelpers;
use Wappointment\Managers\Central;
use Wappointment\Repositories\MustRefreshAvailability;
use Wappointment\WP\StaffLegacy;
class Availability
{
    use MustRefreshAvailability;
    public $daysOfTheWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    public $availabilities = [];
    private $staff_id = false;
    private $days = 0;
    private $timezone = '';
    private $regav = [];
    private $segmentService = null;
    private $staff = null;
    private $isLegacy = true;
    public function __construct($staff_id = false)
    {
        $this->segmentService = new \Wappointment\Services\Segment();
        $this->isLegacy = !\Wappointment\Services\VersionDB::atLeast(\Wappointment\Services\VersionDB::CAN_CREATE_SERVICES);
        if ($this->isLegacy) {
            $this->staff = new \Wappointment\WP\StaffLegacy();
            $this->staff_id = \Wappointment\Services\Settings::get('activeStaffId');
            $this->timezone = \Wappointment\Services\Settings::getStaff('timezone', $this->staff_id);
            $this->regav = \Wappointment\Services\Settings::getStaff('regav', $this->staff_id);
            $this->days = (int) \Wappointment\Services\Settings::getStaff('availaible_booking_days', $this->staff_id);
        } else {
            if (empty($staff_id)) {
                throw new \WappointmentException("Cant regenerate", 1);
            }
            $this->staff = \is_numeric($staff_id) ? \Wappointment\Managers\Central::get('CalendarModel')::findOrFail($staff_id) : $staff_id;
            $this->timezone = $this->staff->options['timezone'];
            $this->regav = $this->staff->options['regav'];
            $this->days = (int) $this->staff->options['avb'];
        }
    }
    public function getMaxTs()
    {
        return \time() + $this->days * 24 * 3600;
    }
    public function returnStaff()
    {
        return $this->staff;
    }
    public function syncAndRegen($forceRegen = false)
    {
        $calendar_urls = $this->staff->getCalendarUrls();
        $hasChanged = false;
        if (!empty($calendar_urls) && \is_array($calendar_urls)) {
            foreach ($calendar_urls as $calurl) {
                if ((new \Wappointment\Services\Calendar($calurl, $this->isLegacy ? $this->staff->id : $this->staff, $this->isLegacy))->fetch()) {
                    $hasChanged = true;
                }
            }
        }
        //regenerate availability only when we get new events
        if ($hasChanged || $forceRegen) {
            $this->regenerate();
        }
    }
    /**
     * Regenerate all  by default ?
     *
     * @param boolean $staff_id
     * @return void
     */
    public function regenerate($save = true)
    {
        // get regular avail and apply for x time from now
        $this->availabilities = $this->generateAvailabilityWithRA();
        // get busy and free time
        $today = \Wappointment\ClassConnect\Carbon::today();
        $end = \Wappointment\ClassConnect\Carbon::createFromTimestamp($this->getMaxTs())->endOfDay();
        //$end = $today->copy()->addDays($this->days);
        $start_at_string = $today->format(WAPPOINTMENT_DB_FORMAT);
        $end_at_string = $end->format(WAPPOINTMENT_DB_FORMAT);
        $statusEventQuery = \Wappointment\Models\Status::where('muted', '<', 1)->where(function ($query) use($end_at_string, $start_at_string) {
            $query->where(function ($qry) use($end_at_string, $start_at_string) {
                $qry->where('start_at', '<', $end_at_string)->where('end_at', '>', $start_at_string);
            })->orWhere('recur', '>', \Wappointment\Models\Status::RECUR_NOT);
        })->orderBy('start_at');
        if (!$this->isLegacy) {
            $statusEventQuery->where('staff_id', $this->staff->id);
        }
        $statusEvents = $statusEventQuery->get();
        $statusBusy = $statusEvents->where('type', \Wappointment\Models\Status::TYPE_BUSY);
        $notrecurringBusy = $statusBusy->where('recur', \Wappointment\Models\Status::RECUR_NOT);
        $recurringBusy = $statusBusy->where('recur', '>', \Wappointment\Models\Status::RECUR_NOT)->all();
        $generatedPunctualEvents = $this->expandRecurring($recurringBusy, $end->timestamp);
        $recurringBusy = null;
        $newBusyStatuses = $notrecurringBusy->concat($generatedPunctualEvents);
        $segmentCollection = $this->segmentService->convertModel($statusEvents->where('type', \Wappointment\Models\Status::TYPE_FREE)->all());
        // get clean free time to increase availability
        $frees = $this->segmentService->flatten($segmentCollection);
        //merge ra and extra free
        $test = \array_merge($this->availabilities, $frees);
        $collection = \WappointmentLv::collect($test)->sortBy(0)->values()->all();
        $this->availabilities = $this->segmentService->flatten($collection);
        // get appointments
        $appointmentQuery = \Wappointment\Models\Appointment::where('start_at', '>=', $start_at_string)->where('status', '>=', \Wappointment\Models\Appointment::STATUS_AWAITING_CONFIRMATION)->where('end_at', '<=', $end_at_string);
        if (!$this->isLegacy) {
            $appointmentQuery->where('staff_id', $this->staff->id);
        }
        $appointments = $appointmentQuery->get();
        $appointments = $this->segmentService->convertModel($appointments);
        $appointments = $this->segmentService->flatten($appointments);
        // substract appointments to availability
        $this->availabilities = $this->segmentService->substract($this->availabilities, $appointments);
        // substract busy times to availability
        $busy_status = $this->segmentService->convertModel($newBusyStatuses->all());
        $busy_status = $this->segmentService->flatten($busy_status);
        $busy_status = $this->reOrder($busy_status);
        $this->availabilities = $this->segmentService->substract($this->availabilities, $busy_status);
        $this->availabilities = $this->reOrder($this->availabilities);
        $result = $save ? $this->isLegacy ? $this->saveLegacy() : $this->save() : $this->availabilities;
        $this->refreshAvailability();
        return $result;
    }
    private function saveLegacy()
    {
        \Wappointment\WP\Helpers::setStaffOption('since_last_refresh', 0, $this->staff_id);
        //save it to the db
        return \Wappointment\WP\Helpers::setStaffOption('availability', $this->availabilities, $this->staff_id);
    }
    public function getLastRefresh()
    {
        return $this->isLegacy ? (int) \Wappointment\WP\Helpers::getStaffOption('since_last_refresh', $this->staff_id, 0) : (int) $this->staff->options['since_last_refresh'];
    }
    public function incrementLastRefresh()
    {
        if ($this->isLegacy) {
            \Wappointment\WP\Helpers::setStaffOption('since_last_refresh', $this->getLastRefresh() + 1, $this->staff_id);
        } else {
            $options = $this->staff->options;
            $options['since_last_refresh'] = $options['since_last_refresh'] + 1;
            //save it to the db
            $this->staff->update(['options' => $options]);
        }
    }
    private function save()
    {
        $options = $this->staff->options;
        $options['since_last_refresh'] = 0;
        //save it to the db
        return $this->staff->update(['availability' => $this->availabilities, 'options' => $options]);
    }
    protected function daysRegenerating()
    {
        if (!\Wappointment\Services\Settings::get('allow_refreshavb')) {
            return $this->days;
        }
        // if it's the daily job and we need to wait for that moment
        return \Wappointment\Services\Settings::get('allow_refreshavb') && \Wappointment\ClassConnect\Carbon::now($this->timezone)->hour >= \Wappointment\Services\Settings::get('refreshavb_at') ? $this->days : $this->days - 1;
    }
    /**
     * avoid values such as 12.37, 13.37 etc ...
     *
     * @return object
     */
    private function correctMinuteValue($carbon)
    {
        $test_array = $this->generate5minarray();
        while (!\in_array($carbon->minute, $test_array)) {
            $carbon->addMinute(1);
        }
        return $carbon;
    }
    private function generate5minarray()
    {
        $array = [];
        $value = 0;
        while ($value < 60) {
            $array[] = $value;
            $value += 5;
        }
        return $array;
    }
    private function generateAvailabilityWithRA()
    {
        $dayNumber = 1;
        $now = \Wappointment\ClassConnect\Carbon::today($this->timezone);
        $min_hours = \Wappointment\Services\Settings::get('hours_before_booking_allowed');
        $min_time = \Wappointment\ClassConnect\Carbon::now($this->timezone);
        $min_time->second = 0;
        if ($min_hours > 0) {
            $int_hours = $min_hours;
            $int_minutes = 0;
            if (\strpos($min_hours, '.') !== false) {
                $int_hours = \floor($min_hours);
                $int_minutes = ($min_hours - $int_hours) * 60;
            }
            if ($int_hours) {
                $min_time->addHours($int_hours);
            }
            if ($int_minutes > 0) {
                $min_time->addMinutes($int_minutes);
            }
        }
        $min_time = $this->correctMinuteValue($min_time);
        $max_time = \Wappointment\ClassConnect\Carbon::now($this->timezone)->addDays($this->daysRegenerating())->endOfDay();
        $availability = [];
        while ($now->timestamp < $max_time->timestamp) {
            $dayName = $this->daysOfTheWeek[$now->dayOfWeek];
            $dailyAvailability = $this->regav[$dayName];
            foreach ($dailyAvailability as $dayTimeblock) {
                $start = new \Wappointment\ClassConnect\Carbon($now->format(WAPPOINTMENT_DB_FORMAT . ':00'), $this->timezone);
                $end = new \Wappointment\ClassConnect\Carbon($now->format(WAPPOINTMENT_DB_FORMAT . ':00'), $this->timezone);
                $unit_added = !empty($this->regav['precise']) ? 'addMinutes' : 'addHours';
                //detect precision mode
                $start->{$unit_added}($dayTimeblock[0]);
                $end->{$unit_added}($dayTimeblock[1]);
                if ($min_time->gte($end)) {
                    continue;
                }
                if ($min_time->gt($start) && \Wappointment\Services\Settings::get('availability_fluid')) {
                    $start = $min_time->copy();
                }
                $availability[] = [$start->timestamp, $end->timestamp];
            }
            $now->addDay();
            $dayNumber++;
        }
        return $availability;
    }
    private function expandRecurring($recurringBusy, $endTs)
    {
        return \Wappointment\Services\Status::expand($recurringBusy, $endTs);
    }
    private function reOrder($arrayToReorder)
    {
        $newOrder = [];
        foreach ($arrayToReorder as $key => $avail) {
            $newOrder[$avail[0]] = $key;
        }
        \ksort($newOrder);
        $newAvail = [];
        foreach ($newOrder as $index) {
            $newAvail[] = $arrayToReorder[$index];
        }
        return $newAvail;
    }
}