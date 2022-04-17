<?php

namespace WappoVendor\Illuminate\Http;

use BadMethodCallException;
use WappoVendor\Illuminate\Support\Str;
use WappoVendor\Illuminate\Support\MessageBag;
use WappoVendor\Illuminate\Support\ViewErrorBag;
use WappoVendor\Illuminate\Support\Traits\Macroable;
use WappoVendor\Illuminate\Session\Store as SessionStore;
use WappoVendor\Illuminate\Contracts\Support\MessageProvider;
use WappoVendor\Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use WappoVendor\Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;
class RedirectResponse extends \WappoVendor\Symfony\Component\HttpFoundation\RedirectResponse
{
    use ResponseTrait, Macroable {
        Macroable::__call as macroCall;
    }
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;
    /**
     * The session store implementation.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;
    /**
     * Flash a piece of data to the session.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return \Illuminate\Http\RedirectResponse
     */
    public function with($key, $value = null)
    {
        $key = \is_array($key) ? $key : [$key => $value];
        foreach ($key as $k => $v) {
            $this->session->flash($k, $v);
        }
        return $this;
    }
    /**
     * Add multiple cookies to the response.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function withCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->headers->setCookie($cookie);
        }
        return $this;
    }
    /**
     * Flash an array of input to the session.
     *
     * @param  array  $input
     * @return $this
     */
    public function withInput(array $input = null)
    {
        $this->session->flashInput($this->removeFilesFromInput(!\is_null($input) ? $input : $this->request->input()));
        return $this;
    }
    /**
     * Remove all uploaded files form the given input array.
     *
     * @param  array  $input
     * @return array
     */
    protected function removeFilesFromInput(array $input)
    {
        foreach ($input as $key => $value) {
            if (\is_array($value)) {
                $input[$key] = $this->removeFilesFromInput($value);
            }
            if ($value instanceof \WappoVendor\Symfony\Component\HttpFoundation\File\UploadedFile) {
                unset($input[$key]);
            }
        }
        return $input;
    }
    /**
     * Flash an array of input to the session.
     *
     * @return $this
     */
    public function onlyInput()
    {
        return $this->withInput($this->request->only(\func_get_args()));
    }
    /**
     * Flash an array of input to the session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exceptInput()
    {
        return $this->withInput($this->request->except(\func_get_args()));
    }
    /**
     * Flash a container of errors to the session.
     *
     * @param  \Illuminate\Contracts\Support\MessageProvider|array|string  $provider
     * @param  string  $key
     * @return $this
     */
    public function withErrors($provider, $key = 'default')
    {
        $value = $this->parseErrors($provider);
        $errors = $this->session->get('errors', new \WappoVendor\Illuminate\Support\ViewErrorBag());
        if (!$errors instanceof \WappoVendor\Illuminate\Support\ViewErrorBag) {
            $errors = new \WappoVendor\Illuminate\Support\ViewErrorBag();
        }
        $this->session->flash('errors', $errors->put($key, $value));
        return $this;
    }
    /**
     * Parse the given errors into an appropriate value.
     *
     * @param  \Illuminate\Contracts\Support\MessageProvider|array|string  $provider
     * @return \Illuminate\Support\MessageBag
     */
    protected function parseErrors($provider)
    {
        if ($provider instanceof \WappoVendor\Illuminate\Contracts\Support\MessageProvider) {
            return $provider->getMessageBag();
        }
        return new \WappoVendor\Illuminate\Support\MessageBag((array) $provider);
    }
    /**
     * Get the original response content.
     *
     * @return null
     */
    public function getOriginalContent()
    {
        //
    }
    /**
     * Get the request instance.
     *
     * @return \Illuminate\Http\Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * Set the request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function setRequest(\WappoVendor\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }
    /**
     * Get the session store implementation.
     *
     * @return \Illuminate\Session\Store|null
     */
    public function getSession()
    {
        return $this->session;
    }
    /**
     * Set the session store implementation.
     *
     * @param  \Illuminate\Session\Store  $session
     * @return void
     */
    public function setSession(\WappoVendor\Illuminate\Session\Store $session)
    {
        $this->session = $session;
    }
    /**
     * Dynamically bind flash data in the session.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
        if (\WappoVendor\Illuminate\Support\Str::startsWith($method, 'with')) {
            return $this->with(\WappoVendor\Illuminate\Support\Str::snake(\substr($method, 4)), $parameters[0]);
        }
        throw new \BadMethodCallException("Method [{$method}] does not exist on Redirect.");
    }
}
