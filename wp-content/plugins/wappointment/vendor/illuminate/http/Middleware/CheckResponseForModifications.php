<?php

namespace WappoVendor\Illuminate\Http\Middleware;

use Closure;
use WappoVendor\Symfony\Component\HttpFoundation\Response;
class CheckResponseForModifications
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        if ($response instanceof \WappoVendor\Symfony\Component\HttpFoundation\Response) {
            $response->isNotModified($request);
        }
        return $response;
    }
}
