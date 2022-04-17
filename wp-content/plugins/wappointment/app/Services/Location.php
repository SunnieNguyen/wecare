<?php

namespace Wappointment\Services;

use Wappointment\Models\Location as LocationModel;
use Wappointment\ClassConnect\RakitValidator;
use Wappointment\Helpers\Translations;
use Wappointment\Managers\Service as ServiceManager;
class Location
{
    public static function save($locationData)
    {
        $validator = new \Wappointment\ClassConnect\RakitValidator();
        $validation_messages = ['type' => __('Select the modality type', 'wappointment'), 'options.address' => __('Enter an address', 'wappointment'), 'options.countries' => __('Select countries that you cover', 'wappointment'), 'options.video' => __('Select video provider', 'wappointment')];
        $validator->setMessages($validation_messages);
        $validationRules = ['name' => 'required|is_adv_string|max:100', 'type' => 'required|numeric', 'options' => '', 'options.address' => 'required_if_has:type,' . \Wappointment\Models\Location::TYPE_AT_LOCATION, 'options.countries' => 'required_if_has:type,' . \Wappointment\Models\Location::TYPE_PHONE . '|array', 'options.video' => 'required_if_has:type,' . \Wappointment\Models\Location::TYPE_ZOOM];
        $validation = $validator->make($locationData, $validationRules);
        $validation->validate();
        if ($validation->fails()) {
            throw new \WappointmentValidationException(\Wappointment\Helpers\Translations::get('error_saving'), 1, null, $validation->errors()->toArray());
        }
        return static::saveLocation($locationData);
    }
    public static function saveLocation($locationData)
    {
        $serviceDB = null;
        if (!empty($locationData['id'])) {
            $serviceDB = \Wappointment\Models\Location::findOrFail($locationData['id']);
        } else {
            if (!\Wappointment\Managers\Service::model()::canCreate()) {
                throw new \WappointmentValidationException(\Wappointment\Helpers\Translations::get('error_saving'));
            }
        }
        return !empty($serviceDB) ? $serviceDB->update($locationData) : \Wappointment\Models\Location::create($locationData);
    }
}
