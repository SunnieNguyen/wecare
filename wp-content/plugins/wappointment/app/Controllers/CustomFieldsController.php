<?php

namespace Wappointment\Controllers;

use Wappointment\Controllers\RestController;
use Wappointment\Managers\Central;
class CustomFieldsController extends \Wappointment\Controllers\RestController
{
    public function get()
    {
        return \Wappointment\Managers\Central::get('CustomFields')::get();
    }
}
