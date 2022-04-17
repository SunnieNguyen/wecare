<?php

namespace WappoVendor\Illuminate\Http\Resources;

use WappoVendor\Illuminate\Support\Str;
use WappoVendor\Illuminate\Pagination\AbstractPaginator;
trait CollectsResources
{
    /**
     * Map the given collection resource into its individual resources.
     *
     * @param  mixed  $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if ($resource instanceof \WappoVendor\Illuminate\Http\Resources\MissingValue) {
            return $resource;
        }
        $collects = $this->collects();
        $this->collection = $collects && !$resource->first() instanceof $collects ? $resource->mapInto($collects) : $resource->toBase();
        return $resource instanceof \WappoVendor\Illuminate\Pagination\AbstractPaginator ? $resource->setCollection($this->collection) : $this->collection;
    }
    /**
     * Get the resource that this resource collects.
     *
     * @return string|null
     */
    protected function collects()
    {
        if ($this->collects) {
            return $this->collects;
        }
        if (\WappoVendor\Illuminate\Support\Str::endsWith(\WappointmentLv::class_basename($this), 'Collection') && \class_exists($class = \WappoVendor\Illuminate\Support\Str::replaceLast('Collection', '', \get_class($this)))) {
            return $class;
        }
    }
    /**
     * Get an iterator for the resource collection.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->collection->getIterator();
    }
}
