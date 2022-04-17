<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\HttpCache;

use WappoVendor\Symfony\Component\HttpFoundation\IpUtils;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpFoundation\Response;
use WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class SubRequestHandler
{
    /**
     * @return Response
     */
    public static function handle(\WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \WappoVendor\Symfony\Component\HttpFoundation\Request $request, $type, $catch)
    {
        // save global state related to trusted headers and proxies
        $trustedProxies = \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedProxies();
        $trustedHeaderSet = \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderSet();
        if (\method_exists(\WappoVendor\Symfony\Component\HttpFoundation\Request::class, 'getTrustedHeaderName')) {
            \WappoVendor\Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedProxies, -1);
            $trustedHeaders = [\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED => \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderName(\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED, false), \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR => \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderName(\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR, false), \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST => \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderName(\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST, false), \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO => \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderName(\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO, false), \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT => \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderName(\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT, false)];
            \WappoVendor\Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedProxies, $trustedHeaderSet);
        } else {
            $trustedHeaders = [\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED => 'FORWARDED', \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR', \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST', \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO', \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT'];
        }
        // remove untrusted values
        $remoteAddr = $request->server->get('REMOTE_ADDR');
        if (!\WappoVendor\Symfony\Component\HttpFoundation\IpUtils::checkIp($remoteAddr, $trustedProxies)) {
            foreach ($trustedHeaders as $key => $name) {
                if ($trustedHeaderSet & $key) {
                    $request->headers->remove($name);
                    $request->server->remove('HTTP_' . \strtoupper(\str_replace('-', '_', $name)));
                }
            }
        }
        // compute trusted values, taking any trusted proxies into account
        $trustedIps = [];
        $trustedValues = [];
        foreach (\array_reverse($request->getClientIps()) as $ip) {
            $trustedIps[] = $ip;
            $trustedValues[] = \sprintf('for="%s"', $ip);
        }
        if ($ip !== $remoteAddr) {
            $trustedIps[] = $remoteAddr;
            $trustedValues[] = \sprintf('for="%s"', $remoteAddr);
        }
        // set trusted values, reusing as much as possible the global trusted settings
        if (\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED & $trustedHeaderSet) {
            $trustedValues[0] .= \sprintf(';host="%s";proto=%s', $request->getHttpHost(), $request->getScheme());
            $request->headers->set($name = $trustedHeaders[\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED], $v = \implode(', ', $trustedValues));
            $request->server->set('HTTP_' . \strtoupper(\str_replace('-', '_', $name)), $v);
        }
        if (\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR & $trustedHeaderSet) {
            $request->headers->set($name = $trustedHeaders[\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR], $v = \implode(', ', $trustedIps));
            $request->server->set('HTTP_' . \strtoupper(\str_replace('-', '_', $name)), $v);
        } elseif (!(\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED & $trustedHeaderSet)) {
            \WappoVendor\Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedProxies, $trustedHeaderSet | \WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR);
            $request->headers->set($name = $trustedHeaders[\WappoVendor\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR], $v = \implode(', ', $trustedIps));
            $request->server->set('HTTP_' . \strtoupper(\str_replace('-', '_', $name)), $v);
        }
        // fix the client IP address by setting it to 127.0.0.1,
        // which is the core responsibility of this method
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        // ensure 127.0.0.1 is set as trusted proxy
        if (!\WappoVendor\Symfony\Component\HttpFoundation\IpUtils::checkIp('127.0.0.1', $trustedProxies)) {
            \WappoVendor\Symfony\Component\HttpFoundation\Request::setTrustedProxies(\array_merge($trustedProxies, ['127.0.0.1']), \WappoVendor\Symfony\Component\HttpFoundation\Request::getTrustedHeaderSet());
        }
        try {
            return $kernel->handle($request, $type, $catch);
        } finally {
            // restore global state
            \WappoVendor\Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedProxies, $trustedHeaderSet);
        }
    }
}
