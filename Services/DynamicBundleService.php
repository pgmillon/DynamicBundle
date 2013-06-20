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
//        $bundles = array();
//        $dynamicBundles = $this->container->getParameter('dynamic.bundles');
//        foreach ($this->container->getParameter('kernel.bundles') as $kernelBundle) {
//            if(in_array($kernelBundle, $dynamicBundles)) {
//                $bundles[] = $kernelBundle;
//            }
//        }
        return $this->container->getParameter('dynamic.bundles');
    }

    public function getDynamicBundlesDir()
    {
        return $this->container->getParameter('dynamic.bundles_dir');
    }

    public function getAvailableBundles()
    {
        $bundles = array();
        $bundlesDir = $this->getDynamicBundlesDir();
        $filesystem = new Filesystem();
        $finder = new Finder();
        $finder->name('*Bundle.php')->in($bundlesDir);
        foreach ($finder as $file) {
            /* @var $file \SplFileInfo */
            $className = $file->getBasename('.php');
            $relativePath = $filesystem->makePathRelative($file->getPath(), $bundlesDir);
            $bundles[] = str_replace('/', '\\', trim($relativePath, '/')) . '\\' . $className;
        }
        return $bundles;
    }
}
