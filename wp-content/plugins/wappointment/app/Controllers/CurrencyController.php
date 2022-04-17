<?php

namespace Wappointment\Controllers;

use Wappointment\ClassConnect\Request;
use Wappointment\Helpers\Get;
use Wappointment\Helpers\Translations;
use Wappointment\Services\Settings;
class CurrencyController extends \Wappointment\Controllers\RestController
{
    public function get(\Wappointment\ClassConnect\Request $request)
    {
        return \Wappointment\Helpers\Get::list('currencies');
    }
    public function save(\Wappointment\ClassConnect\Request $request)
    {
        if (\strlen($request->input('currency')) > 3) {
            throw new \WappointmentException('Currency is not correct', 1);
        }
        \Wappointment\Services\Settings::save('currency', $request->input('currency'));
        return ['message' => \Wappointment\Helpers\Translations::get('element_saved')];
    }
}
