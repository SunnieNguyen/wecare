<?php

namespace WappoVendor\Illuminate\Http\Resources\Json;

use IteratorAggregate;
use WappoVendor\Illuminate\Pagination\AbstractPaginator;
use WappoVendor\Illuminate\Http\Resources\CollectsResources;
class ResourceCollection extends \WappoVendor\Illuminate\Http\Resources\Json\Resource implements \IteratorAggregate
{
    use CollectsResources;
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects;
    /**
     * The mapped collection instance.
     *
     * @var \Illuminate\Support\Collection
     */
    public $collection;
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->resource = $this->collectResource($resource);
    }
    /**
     * Transform the resource into a JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map->toArray($request)->all();
    }
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof \WappoVendor\Illuminate\Pagination\AbstractPaginator ? (new \WappoVendor\Illuminate\Http\Resources\Json\PaginatedResourceResponse($this))->toResponse($request) : parent::toResponse($request);
    }
}
