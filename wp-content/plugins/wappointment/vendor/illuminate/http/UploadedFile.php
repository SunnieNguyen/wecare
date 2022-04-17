<?php

namespace WappoVendor\Illuminate\Http;

use WappoVendor\Illuminate\Support\Arr;
use WappoVendor\Illuminate\Container\Container;
use WappoVendor\Illuminate\Support\Traits\Macroable;
use WappoVendor\Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use WappoVendor\Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
class UploadedFile extends \WappoVendor\Symfony\Component\HttpFoundation\File\UploadedFile
{
    use FileHelpers, Macroable;
    /**
     * Begin creating a new file fake.
     *
     * @return \Illuminate\Http\Testing\FileFactory
     */
    public static function fake()
    {
        return new \WappoVendor\Illuminate\Http\Testing\FileFactory();
    }
    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string  $path
     * @param  array|string  $options
     * @return string|false
     */
    public function store($path, $options = [])
    {
        return $this->storeAs($path, $this->hashName(), $this->parseOptions($options));
    }
    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param  string  $path
     * @param  array|string  $options
     * @return string|false
     */
    public function storePublicly($path, $options = [])
    {
        $options = $this->parseOptions($options);
        $options['visibility'] = 'public';
        return $this->storeAs($path, $this->hashName(), $options);
    }
    /**
     * Store the uploaded file on a filesystem disk with public visibility.
     *
     * @param  string  $path
     * @param  string  $name
     * @param  array|string  $options
     * @return string|false
     */
    public function storePubliclyAs($path, $name, $options = [])
    {
        $options = $this->parseOptions($options);
        $options['visibility'] = 'public';
        return $this->storeAs($path, $name, $options);
    }
    /**
     * Store the uploaded file on a filesystem disk.
     *
     * @param  string  $path
     * @param  string  $name
     * @param  array|string  $options
     * @return string|false
     */
    public function storeAs($path, $name, $options = [])
    {
        $options = $this->parseOptions($options);
        $disk = \WappoVendor\Illuminate\Support\Arr::pull($options, 'disk');
        return \WappoVendor\Illuminate\Container\Container::getInstance()->make(\WappoVendor\Illuminate\Contracts\Filesystem\Factory::class)->disk($disk)->putFileAs($path, $this, $name, $options);
    }
    /**
     * Create a new file instance from a base instance.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @param  bool $test
     * @return static
     */
    public static function createFromBase(\WappoVendor\Symfony\Component\HttpFoundation\File\UploadedFile $file, $test = false)
    {
        return $file instanceof static ? $file : new static($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType(), $file->getClientSize(), $file->getError(), $test);
    }
    /**
     * Parse and format the given options.
     *
     * @param  array|string  $options
     * @return array
     */
    protected function parseOptions($options)
    {
        if (\is_string($options)) {
            $options = ['disk' => $options];
        }
        return $options;
    }
}
