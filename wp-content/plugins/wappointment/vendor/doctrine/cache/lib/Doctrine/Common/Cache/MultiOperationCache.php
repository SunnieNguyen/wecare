<?php

namespace WappoVendor\Doctrine\Common\Cache;

/**
 * Interface for cache drivers that supports multiple items manipulation.
 *
 * @link   www.doctrine-project.org
 */
interface MultiOperationCache extends \WappoVendor\Doctrine\Common\Cache\MultiGetCache, \WappoVendor\Doctrine\Common\Cache\MultiDeleteCache, \WappoVendor\Doctrine\Common\Cache\MultiPutCache
{
}
