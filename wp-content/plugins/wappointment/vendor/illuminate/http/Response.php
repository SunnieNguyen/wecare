<?php

namespace WappoVendor\Illuminate\Http;

use ArrayObject;
use JsonSerializable;
use WappoVendor\Illuminate\Support\Traits\Macroable;
use WappoVendor\Illuminate\Contracts\Support\Jsonable;
use WappoVendor\Illuminate\Contracts\Support\Arrayable;
use WappoVendor\Illuminate\Contracts\Support\Renderable;
use WappoVendor\Symfony\Component\HttpFoundation\Response as BaseResponse;
class Response extends \WappoVendor\Symfony\Component\HttpFoundation\Response
{
    use ResponseTrait, Macroable {
        Macroable::__call as macroCall;
    }
    /**
     * Set the content on the response.
     *
     * @param  mixed  $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->original = $content;
        // If the content is "JSONable" we will set the appropriate header and convert
        // the content to JSON. This is useful when returning something like models
        // from routes that will be automatically transformed to their JSON form.
        if ($this->shouldBeJson($content)) {
            $this->header('Content-Type', 'application/json');
            $content = $this->morphToJson($content);
        } elseif ($content instanceof \WappoVendor\Illuminate\Contracts\Support\Renderable) {
            $content = $content->render();
        }
        parent::setContent($content);
        return $this;
    }
    /**
     * Determine if the given content should be turned into JSON.
     *
     * @param  mixed  $content
     * @return bool
     */
    protected function shouldBeJson($content)
    {
        return $content instanceof \WappoVendor\Illuminate\Contracts\Support\Arrayable || $content instanceof \WappoVendor\Illuminate\Contracts\Support\Jsonable || $content instanceof \ArrayObject || $content instanceof \JsonSerializable || \is_array($content);
    }
    /**
     * Morph the given content into JSON.
     *
     * @param  mixed   $content
     * @return string
     */
    protected function morphToJson($content)
    {
        if ($content instanceof \WappoVendor\Illuminate\Contracts\Support\Jsonable) {
            return $content->toJson();
        } elseif ($content instanceof \WappoVendor\Illuminate\Contracts\Support\Arrayable) {
            return \json_encode($content->toArray());
        }
        return \json_encode($content);
    }
}
