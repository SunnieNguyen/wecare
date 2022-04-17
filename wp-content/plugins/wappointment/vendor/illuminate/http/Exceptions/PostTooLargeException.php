<?php

namespace WappoVendor\Illuminate\Http\Exceptions;

use Exception;
use WappoVendor\Symfony\Component\HttpKernel\Exception\HttpException;
class PostTooLargeException extends \WappoVendor\Symfony\Component\HttpKernel\Exception\HttpException
{
    /**
     * PostTooLargeException constructor.
     *
     * @param  string|null  $message
     * @param  \Exception|null  $previous
     * @param  array  $headers
     * @param  int  $code
     * @return void
     */
    public function __construct($message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(413, $message, $previous, $headers, $code);
    }
}
