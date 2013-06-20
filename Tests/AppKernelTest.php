<?php

namespace IneatConseil\DynamicBundle\Tests;

use IneatConseil\DynamicBundle\HttpKernel\DynamicAppKernel;

/**
 * Description of AppKernelTest
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
class AppKernelTest extends DynamicAppKernel
{
    protected $cacheDir;

    public function __construct($env='test', $cacheDir, $debug = true)
    {
        parent::__construct($env, $debug);
        $this->cacheDir = $cacheDir;
    }

    public function setDynamicBundlesConfigurationFile($dynamicBundlesConfigurationFile)
    {
        $this->dynamicBundlesConfigurationFile = $dynamicBundlesConfigurationFile;
    }

    public function getCacheDir()
    {
        return $this->cacheDir;
    }
    
    public function getRootDir()
    {
        $r = new \ReflectionClass('\AppKernel');
        $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        
        return parent::getRootDir();
    }

    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \IneatConseil\DynamicBundle\IneatConseilDynamicBundle(),
        );

        return $this->registerDynamicBundles($bundles);
    }

    public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);
        $loader->load($this->getRootDir() . '/config/config.yml');
    }
}
