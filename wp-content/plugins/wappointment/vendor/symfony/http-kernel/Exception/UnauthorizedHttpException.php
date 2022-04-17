<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\Exception;

/**
 * @author Ben Ramsey <ben@benramsey.com>
 */
class UnauthorizedHttpException extends \WappoVendor\Symfony\Component\HttpKernel\Exception\HttpException
{
    /**
     * @param string     $challenge WWW-Authenticate challenge string
     * @param string     $message   The internal exception message
     * @param \Exception $previous  The previous exception
     * @param int        $code      The internal exception code
     */
    public function __construct($challenge, $message = null, \Exception $previous = null, $code = 0)
    {
        $headers = ['WWW-Authenticate' => $challenge];
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}