<?php

namespace IneatConseil\DynamicBundle\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of DynamicBundleService
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
class DynamicBundleService
{
    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getActivatedBundles()
    {
        return $this->container->getParameter('dynamic.bundles');
    }
    
    public function getDynamicBundlesDir()
    {
        return $this->container->getParameter('dynamic.bundles_dir');
    }

    public function getAvailableBundles()
    {
        $bundles = [];
        $bundlesDir = $this->getDynamicBundlesDir();
        $filesystem = new Filesystem();
        $finder = new Finder();
        $finder->name('*Bundle.php')->in($bundlesDir);
        foreach($finder as $file) {
            /* @var $file \SplFileInfo */
            $className = $file->getBasename('.php');
            $relativePath = $filesystem->makePathRelative($file->getPath(), $bundlesDir);
            $bundles[] = str_replace('/', '\\', trim($relativePath, '/')).'\\'.$className;
        }
        return $bundles;
    }
}
