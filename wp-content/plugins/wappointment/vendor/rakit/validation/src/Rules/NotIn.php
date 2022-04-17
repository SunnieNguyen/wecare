<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class NotIn extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not allowing :value";
    protected $strict = false;
    public function fillParameters(array $params)
    {
        if (\count($params) == 1 and \is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['disallowed_values'] = $params;
        return $this;
    }
    public function strict($strict = true)
    {
        $this->strict = $strict;
    }
    public function check($value)
    {
        $this->requireParameters(['disallowed_values']);
        $disallowed_values = (array) $this->parameter('disallowed_values');
        return !\in_array($value, $disallowed_values, $this->strict);
    }
}
