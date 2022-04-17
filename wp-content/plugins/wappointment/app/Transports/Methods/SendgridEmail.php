<?php

namespace Wappointment\Transports\Methods;

use Wappointment\Transports\Sendgrid;
class SendgridEmail implements \Wappointment\Transports\Methods\InterfaceEmailTransport
{
    public function setMethod($config)
    {
        return new \Wappointment\Transports\Sendgrid(new \WappoVendor\GuzzleHttp\Client(['connect_timeout' => 60]), $config['sgkey']);
    }
}
