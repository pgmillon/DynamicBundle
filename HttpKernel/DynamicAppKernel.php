<?php

namespace IneatConseil\DynamicBundle\HttpKernel;

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
        $loader->load($this->getDynamicBundlesConfigurationFile());
    }
    
}
