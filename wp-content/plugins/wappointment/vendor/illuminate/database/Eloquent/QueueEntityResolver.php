<?php

namespace WappoVendor\Illuminate\Database\Eloquent;

use WappoVendor\Illuminate\Contracts\Queue\EntityNotFoundException;
use WappoVendor\Illuminate\Contracts\Queue\EntityResolver as EntityResolverContract;
class QueueEntityResolver implements \WappoVendor\Illuminate\Contracts\Queue\EntityResolver
{
    /**
     * Resolve the entity for the given ID.
     *
     * @param  string  $type
     * @param  mixed  $id
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Queue\EntityNotFoundException
     */
    public function resolve($type, $id)
    {
        $instance = (new $type())->find($id);
        if ($instance) {
            return $instance;
        }
        throw new \WappoVendor\Illuminate\Contracts\Queue\EntityNotFoundException($type, $id);
    }
}
