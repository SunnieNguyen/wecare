<?php

namespace WappoVendor\Illuminate\Contracts\Filesystem;

interface Cloud extends \WappoVendor\Illuminate\Contracts\Filesystem\Filesystem
{
    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     */
    public function url($path);
}
