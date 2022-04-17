<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\Config;

use WappoVendor\Symfony\Component\Config\FileLocator as BaseFileLocator;
use WappoVendor\Symfony\Component\HttpKernel\KernelInterface;
/**
 * FileLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLocator extends \WappoVendor\Symfony\Component\Config\FileLocator
{
    private $kernel;
    private $path;
    /**
     * @param KernelInterface $kernel A KernelInterface instance
     * @param string|null     $path   The path the global resource directory
     * @param array           $paths  An array of paths where to look for resources
     */
    public function __construct(\WappoVendor\Symfony\Component\HttpKernel\KernelInterface $kernel, $path = null, array $paths = [])
    {
        $this->kernel = $kernel;
        if (null !== $path) {
            $this->path = $path;
            $paths[] = $path;
        }
        parent::__construct($paths);
    }
    /**
     * {@inheritdoc}
     */
    public function locate($file, $currentPath = null, $first = true)
    {
        if (isset($file[0]) && '@' === $file[0]) {
            return $this->kernel->locateResource($file, $this->path, $first);
        }
        return parent::locate($file, $currentPath, $first);
    }
}
