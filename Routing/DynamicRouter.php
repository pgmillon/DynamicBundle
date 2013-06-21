<?php

namespace IneatConseil\DynamicBundle\Routing;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use IneatConseil\DynamicBundle\Config\DynamicConfigCache;
use IneatConseil\DynamicBundle\HttpKernel\DynamicAppKernel;
use IneatConseil\DynamicBundle\Services\DynamicBundleService;
use IneatConseil\DynamicBundle\Bundle\DynamicBundle;

/**
 * Description of DynamicRouter
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
class DynamicRouter extends Router
{

    /**
     *
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     *
     * @var DynamicBundleService
     */
    protected $bundleService;

    public function __construct(ContainerInterface $container, $resource, array $options = array(), RequestContext $context = null)
    {
        parent::__construct($container, $resource, $options, $context);
        $this->container = $container;
        $this->bundleService = $this->getContainer()->get($this->getKernel()->getDynamicBundlesServiceName());
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $collection = parent::getRouteCollection();
            $this->collection = new RouteCollection();
            $this->collection->addCollection($collection);
            $this->collection->addResource(new FileResource($this->getKernel()->getDynamicBundlesConfigurationFile()));
            
            foreach($this->getBundleService()->getActivatedBundles() as $bundle) {
                /* @var $bundle DynamicBundle */
                $this->collection->addCollection($bundle->getRouteCollection());
            }
        }

        return $this->collection;
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            return $this->matcher = new $this->options['matcher_class']($this->getRouteCollection(), $this->context);
        }

        $class = $this->options['matcher_cache_class'];
        $cache = new DynamicConfigCache($this->getKernel(), $this->options['cache_dir'].'/'.$class.'.php', $this->options['debug']);
        if (!$cache->isFresh($class)) {
            $dumper = new $this->options['matcher_dumper_class']($this->getRouteCollection());

            $options = array(
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            );

            $cache->write($dumper->dump($options), $this->getRouteCollection()->getResources());
        }

        require_once $cache;

        return $this->matcher = new $class($this->context);
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
    
    /**
     * 
     * @return DynamicBundleService
     */
    public function getBundleService()
    {
        return $this->bundleService;
    }

}
