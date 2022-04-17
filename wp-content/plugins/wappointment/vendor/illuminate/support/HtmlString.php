<?php

namespace WappoVendor\Illuminate\Support;

use WappoVendor\Illuminate\Contracts\Support\Htmlable;
class HtmlString implements \WappoVendor\Illuminate\Contracts\Support\Htmlable
{
    /**
     * The HTML string.
     *
     * @var string
     */
    protected $html;
    /**
     * Create a new HTML string instance.
     *
     * @param  string  $html
     * @return void
     */
    public function __construct($html)
    {
        $this->html = $html;
    }
    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->html;
    }
    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
