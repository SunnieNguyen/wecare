<?php

namespace Wappointment\Controllers;

use Wappointment\WP\Helpers as WPHelpers;
use Wappointment\ClassConnect\Request;
use Wappointment\Services\Settings;
use Wappointment\Services\Reset;
class WizardController extends \Wappointment\Controllers\RestController
{
    private $last_step = 4;
    /**
     * Legacy TODO remove
     */
    public function later(\Wappointment\ClassConnect\Request $request)
    {
        \Wappointment\WP\Helpers::setOption('wizard_step', -1);
        return ['message' => 'Allright, you can come back here whenever you want.'];
    }
    public function setStep(\Wappointment\ClassConnect\Request $request)
    {
        if ($request->input('step') == 1) {
            new \Wappointment\Installation\Process();
        }
        if (\in_array($request->input('step'), [2, 3])) {
            \Wappointment\Services\Reset::refreshCache();
        }
        \Wappointment\WP\Helpers::setOption('wizard_step', $request->input('step'));
        if ($this->last_step == $request->input('step')) {
            if (!empty($request->input('booking_page_id'))) {
                \Wappointment\Services\Settings::save('booking_page', (int) $request->input('booking_page_id'));
            }
            return ['message' => __('Done with the setup. Let\'s fill this agenda of yours!', 'wappointment')];
        }
        return true;
    }
}
