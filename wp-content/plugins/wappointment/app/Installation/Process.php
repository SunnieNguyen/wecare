<?php

namespace Wappointment\Installation;

use Wappointment\WP\Helpers as WPHelpers;
use Wappointment\Services\Settings;
use Wappointment\System\Status;
class Process extends \Wappointment\Installation\AbstractProcess
{
    protected $key = 'installation_step';
    protected $steps = ['Wappointment\\Installation\\Steps\\CreateMigrationTable', 'Wappointment\\Installation\\Steps\\CreateTables', true];
    protected function isUpToDate()
    {
        if (empty(\Wappointment\WP\Helpers::getOption('installation_completed'))) {
            return false;
        }
        return true;
    }
    protected function completed()
    {
        (new \Wappointment\WP\CustomPage())->install();
        $this->complete = true;
        $mail_config = \Wappointment\Services\Settings::get('mail_config');
        $mail_config['wpmail_html'] = true;
        \Wappointment\Services\Settings::saveMultiple(['mail_config' => $mail_config, 'show_welcome' => true, 'activeStaffId' => (int) \Wappointment\WP\Helpers::userId(), 'email_notifications' => \Wappointment\WP\Helpers::currentUserEmail()]);
        \Wappointment\System\Status::dbVersionUpdateComplete();
        \Wappointment\System\Status::setViewedUpdated();
        return \Wappointment\WP\Helpers::setOption('installation_completed', (int) current_time('timestamp'), true);
    }
}
