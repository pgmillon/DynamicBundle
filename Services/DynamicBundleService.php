<?php

namespace IneatConseil\DynamicBundle\Services;

/**
 * Description of DynamicBundleService
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
class DynamicBundleService
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
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

    public function getAvailableBundles()
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->name('*Bundle.php')->in(__DIR__);
        return array();
    }
}
