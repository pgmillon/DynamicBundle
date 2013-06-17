<?php

namespace IneatConseil\DynamicBundle\HttpKernel;

use IneatConseil\DynamicBundle\Config\DynamicConfigCache;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Description of DynamicAppKernel
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
abstract class DynamicAppKernel extends Kernel
{
    protected $dynamicBundlesConfigurationFile;

    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
        $this->dynamicBundlesConfigurationFile = $this->getRootDir() . '/config/dynamicBundles.yml';
    }

    public function getDynamicBundlesConfigurationFile()
    {
        return $this->dynamicBundlesConfigurationFile;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if(is_readable($this->getDynamicBundlesConfigurationFile())) {
            $loader->load($this->getDynamicBundlesConfigurationFile());
        }
    }
    
    /**
     * Initializes the service container.
     *
     * The cached version of the service container is used when fresh, otherwise the
     * container is built.
     */
    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $cache = new DynamicConfigCache($this->dynamicBundlesConfigurationFile, $this->getCacheDir() . '/' . $class . '.php', $this->debug);
        $fresh = true;
        if (!$cache->isFresh()) {
            $container = $this->buildContainer();
            $container->compile();
            $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

            $fresh = false;
        }

        require_once $cache;

        $this->container = new $class();
        $this->container->set('kernel', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
    }


}
