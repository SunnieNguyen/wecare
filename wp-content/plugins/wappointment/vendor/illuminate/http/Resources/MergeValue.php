<?php

namespace WappoVendor\Illuminate\Http\Resources;

use WappoVendor\Illuminate\Support\Collection;
class MergeValue
{
    /**
     * The data to be merged.
     *
     * @var array
     */
    public $data;
    /**
     * Create new merge value instance.
     *
     * @param  \Illuminate\Support\Collection|array  $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data instanceof \WappoVendor\Illuminate\Support\Collection ? $data->all() : $data;
    }
}
