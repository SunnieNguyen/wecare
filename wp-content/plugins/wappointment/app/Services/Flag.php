<?php

namespace Wappointment\Services;

use Wappointment\WP\Helpers as WPHelpers;
class Flag
{
    public static function save($flag, $value)
    {
        $flags = static::get();
        $flags[$flag] = $value;
        \Wappointment\WP\Helpers::setOption('flags', $flags);
    }
    public static function get($flag = false)
    {
        $flags = \Wappointment\WP\Helpers::getOption('flags');
        return $flag !== false ? static::getValue($flag, $flags) : $flags;
    }
    protected static function getValue($flag, $flags)
    {
        return !empty($flags[$flag]) ? $flags[$flag] : false;
    }
}
