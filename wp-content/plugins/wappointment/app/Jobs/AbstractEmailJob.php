<?php

namespace Wappointment\Jobs;

abstract class AbstractEmailJob extends \Wappointment\Jobs\AbstractTransportableJob
{
    use IsEmailableJob, IsAdminAppointmentJob;
}
