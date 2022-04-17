<?php

namespace Wappointment\Jobs;

use Wappointment\Services\Queue;
class AdminEmailWeeklySummary extends \Wappointment\Jobs\AdminEmailDailySummary
{
    const CONTENT = '\\Wappointment\\Messages\\AdminWeeklySummaryEmail';
    public function afterHandled()
    {
        \Wappointment\Services\Queue::queueWeeklyJob(!empty($this->params['staff_id']) ? $this->params['staff_id'] : false);
    }
}
