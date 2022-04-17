<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Required extends \WappoVendor\Rakit\Validation\Rule
{
    use FileTrait;
    protected $implicit = true;
    protected $message = "The :attribute is required";
    public function check($value)
    {
        $this->setAttributeAsRequired();
        if ($this->attribute and $this->attribute->hasRule('uploaded_file')) {
            return $this->isValueFromUploadedFiles($value) and $value['error'] != \UPLOAD_ERR_NO_FILE;
        }
        if (\is_string($value)) {
            return \strlen(\trim($value)) > 0;
        }
        if (\is_array($value)) {
            return \count($value) > 0;
        }
        return !\is_null($value);
    }
    protected function setAttributeAsRequired()
    {
        if ($this->attribute) {
            $this->attribute->setRequired(true);
        }
    }
}
