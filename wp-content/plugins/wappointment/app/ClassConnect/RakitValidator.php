<?php

namespace Wappointment\ClassConnect;

use Wappointment\Validators\HasValues;
use Wappointment\Validators\IsAdvancedString;
use Wappointment\Validators\IsString;
use Wappointment\Validators\RequiredIfHas;
class RakitValidator extends \WappoVendor\Rakit\Validation\Validator
{
    public function __construct(array $messages = [])
    {
        parent::__construct($messages);
        $this->addValidator('hasvalues', new \Wappointment\Validators\HasValues());
        $this->addValidator('required_if_has', new \Wappointment\Validators\RequiredIfHas());
        $this->addValidator('is_string', new \Wappointment\Validators\IsString());
        $this->addValidator('is_adv_string', new \Wappointment\Validators\IsAdvancedString());
    }
}
