<?php

namespace WappoVendor\Rakit\Validation;

use WappoVendor\Rakit\Validation\MissingRequiredParameterException;
abstract class Rule
{
    protected $key;
    protected $attribute;
    protected $validation;
    protected $implicit = false;
    protected $params = [];
    protected $fillable_params = [];
    protected $message = "The :attribute is invalid";
    public abstract function check($value);
    public function setValidation(\WappoVendor\Rakit\Validation\Validation $validation)
    {
        $this->validation = $validation;
    }
    public function setKey($key)
    {
        $this->key = $key;
    }
    public function getKey()
    {
        return $this->key ?: \get_class($this);
    }
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }
    public function getAttribute()
    {
        return $this->attribute ?: \get_class($this);
    }
    public function getParameters()
    {
        return $this->params;
    }
    public function setParameters(array $params)
    {
        $this->params = \array_merge($this->params, $params);
        return $this;
    }
    public function setParameter($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }
    public function fillParameters(array $params)
    {
        foreach ($this->fillable_params as $key) {
            if (empty($params)) {
                break;
            }
            $this->params[$key] = \array_shift($params);
        }
        return $this;
    }
    public function parameter($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }
    public function isImplicit()
    {
        return $this->implicit === true;
    }
    public function message($message)
    {
        return $this->setMessage($message);
    }
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }
    protected function requireParameters(array $params)
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                $rule = $this->getKey();
                throw new \WappoVendor\Rakit\Validation\MissingRequiredParameterException("Missing required parameter '{$param}' on rule '{$rule}'");
            }
        }
    }
}
