<?php

namespace WappoVendor\Rakit\Validation;

class Validator
{
    protected $messages = [];
    protected $validators = [];
    protected $allowRuleOverride = false;
    protected $useHumanizedKeys = true;
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
        $this->registerBaseValidators();
    }
    public function setMessage($key, $message)
    {
        return $this->messages[$key] = $message;
    }
    public function setMessages($messages)
    {
        $this->messages = \array_merge($this->messages, $messages);
    }
    public function setValidator($key, \WappoVendor\Rakit\Validation\Rule $rule)
    {
        $this->validators[$key] = $rule;
        $rule->setKey($key);
    }
    public function getValidator($key)
    {
        return isset($this->validators[$key]) ? $this->validators[$key] : null;
    }
    public function validate(array $inputs, array $rules, array $messages = array())
    {
        $validation = $this->make($inputs, $rules, $messages);
        $validation->validate();
        return $validation;
    }
    public function make(array $inputs, array $rules, array $messages = array())
    {
        $messages = \array_merge($this->messages, $messages);
        return new \WappoVendor\Rakit\Validation\Validation($this, $inputs, $rules, $messages);
    }
    public function __invoke($rule)
    {
        $args = \func_get_args();
        $rule = \array_shift($args);
        $params = $args;
        $validator = $this->getValidator($rule);
        if (!$validator) {
            throw new \WappoVendor\Rakit\Validation\RuleNotFoundException("Validator '{$rule}' is not registered", 1);
        }
        $clonedValidator = clone $validator;
        $clonedValidator->fillParameters($params);
        return $clonedValidator;
    }
    protected function registerBaseValidators()
    {
        $baseValidator = ['required' => new \WappoVendor\Rakit\Validation\Rules\Required(), 'required_if' => new \WappoVendor\Rakit\Validation\Rules\RequiredIf(), 'required_unless' => new \WappoVendor\Rakit\Validation\Rules\RequiredUnless(), 'required_with' => new \WappoVendor\Rakit\Validation\Rules\RequiredWith(), 'required_without' => new \WappoVendor\Rakit\Validation\Rules\RequiredWithout(), 'required_with_all' => new \WappoVendor\Rakit\Validation\Rules\RequiredWithAll(), 'required_without_all' => new \WappoVendor\Rakit\Validation\Rules\RequiredWithoutAll(), 'email' => new \WappoVendor\Rakit\Validation\Rules\Email(), 'alpha' => new \WappoVendor\Rakit\Validation\Rules\Alpha(), 'numeric' => new \WappoVendor\Rakit\Validation\Rules\Numeric(), 'alpha_num' => new \WappoVendor\Rakit\Validation\Rules\AlphaNum(), 'alpha_dash' => new \WappoVendor\Rakit\Validation\Rules\AlphaDash(), 'in' => new \WappoVendor\Rakit\Validation\Rules\In(), 'not_in' => new \WappoVendor\Rakit\Validation\Rules\NotIn(), 'min' => new \WappoVendor\Rakit\Validation\Rules\Min(), 'max' => new \WappoVendor\Rakit\Validation\Rules\Max(), 'between' => new \WappoVendor\Rakit\Validation\Rules\Between(), 'url' => new \WappoVendor\Rakit\Validation\Rules\Url(), 'ip' => new \WappoVendor\Rakit\Validation\Rules\Ip(), 'ipv4' => new \WappoVendor\Rakit\Validation\Rules\Ipv4(), 'ipv6' => new \WappoVendor\Rakit\Validation\Rules\Ipv6(), 'array' => new \WappoVendor\Rakit\Validation\Rules\TypeArray(), 'same' => new \WappoVendor\Rakit\Validation\Rules\Same(), 'regex' => new \WappoVendor\Rakit\Validation\Rules\Regex(), 'date' => new \WappoVendor\Rakit\Validation\Rules\Date(), 'accepted' => new \WappoVendor\Rakit\Validation\Rules\Accepted(), 'present' => new \WappoVendor\Rakit\Validation\Rules\Present(), 'different' => new \WappoVendor\Rakit\Validation\Rules\Different(), 'uploaded_file' => new \WappoVendor\Rakit\Validation\Rules\UploadedFile(), 'callback' => new \WappoVendor\Rakit\Validation\Rules\Callback(), 'before' => new \WappoVendor\Rakit\Validation\Rules\Before(), 'after' => new \WappoVendor\Rakit\Validation\Rules\After(), 'defaults' => new \WappoVendor\Rakit\Validation\Rules\Defaults(), 'default' => new \WappoVendor\Rakit\Validation\Rules\Defaults()];
        foreach ($baseValidator as $key => $validator) {
            $this->setValidator($key, $validator);
        }
    }
    public function addValidator($ruleName, \WappoVendor\Rakit\Validation\Rule $rule)
    {
        if (!$this->allowRuleOverride && \array_key_exists($ruleName, $this->validators)) {
            throw new \WappoVendor\Rakit\Validation\RuleQuashException("You cannot override a built in rule. You have to rename your rule");
        }
        $this->setValidator($ruleName, $rule);
    }
    public function allowRuleOverride($status = false)
    {
        $this->allowRuleOverride = $status;
    }
    public function setUseHumanizedKeys($useHumanizedKeys = true)
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }
    public function getUseHumanizedKeys()
    {
        return $this->useHumanizedKeys;
    }
}
