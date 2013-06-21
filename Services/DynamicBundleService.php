<?php

namespace IneatConseil\DynamicBundle\Services;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use IneatConseil\DynamicBundle\HttpKernel\DynamicAppKernel;
use IneatConseil\DynamicBundle\Bundle\DynamicBundle;

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

    /**
     * 
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * 
     * @return DynamicAppKernel
     */
    public function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }
    
    public function getDynamicBundlesOption()
    {
        return $this->getKernel()->getDynamicBundlesConfigurationOption();
    }

    public function getDynamicBundlesDir()
    {
        return $this->getContainer()->getParameter('dynamic.bundles_dir');
    }

    public function getActivatedBundles()
    {
        $bundles = array();
        foreach ($this->getKernel()->getBundles() as $bundle) {
            if($bundle instanceof DynamicBundle) {
                $bundles[] = $bundle;
            }
        }
        return $bundles;
    }
    
    public function getActivatedBundlesFQDN()
    {
        $bundles = array();
        foreach ($this->getActivatedBundles() as $kernelBundle) {
            /* @var $kernelBundle Bundle */
            $kernelBundleFQDN = $kernelBundle->getNamespace().'\\'.$kernelBundle->getName();
            $bundles[] = $kernelBundleFQDN;
        }
        return $bundles;
    }
    
    public function setActivatedBundles($bundles)
    {
        $dynamicBundlesConfigurationFile = $this->getKernel()->getDynamicBundlesConfigurationFile();
        file_put_contents($dynamicBundlesConfigurationFile, Yaml::dump(array('parameters' => array(
            $this->getDynamicBundlesOption() => $bundles
        ))));
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
