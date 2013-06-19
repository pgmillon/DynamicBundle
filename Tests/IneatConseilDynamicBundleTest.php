<?php

namespace IneatConseil\DynamicBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of IneatConseilDynamicBundleTest
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
class IneatConseilDynamicBundleTest extends WebTestCase
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    
    /**
     *
     * @var \IneatConseil\DynamicBundle\Services\DynamicBundleService
     */
    protected $bundleService;
    protected $acmeSimpleBundleFQDN = 'Acme\SimpleBundle\AcmeSimpleBundle';
    protected $acmeSimpleBundle2FQDN = 'Acme\SimpleBundle2\AcmeSimpleBundle2';

    protected function setUp()
    {
        parent::setUp();

        static::createClient();

        $this->setContainer(static::$kernel->getContainer());
        $this->setBundleService($this->getContainer()->get('dynamic_bundle_service'));

        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
        $cacheClearer = $this->getContainer()->get('cache_clearer');
        /* @var $cacheClearer \Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface */

        $cacheClearer->clear($cacheDir);
    }

    /**
     * 
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
    
    /**
     * 
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function getBundleService()
    {
        return $this->bundleService;
    }

    public function setBundleService(\IneatConseil\DynamicBundle\Services\DynamicBundleService $bundleService)
    {
        $this->bundleService = $bundleService;
    }

    /**
     * @test
     */
    public function iCanListAvailableBundles()
    {
        $bundles = $this->getBundleService()->getAvailableBundles();
        $this->assertContains('Acme\SimpleBundle\AcmeSimpleBundle', $bundles);
        $this->assertContains('Acme\Simple2Bundle\AcmeSimple2Bundle', $bundles);
    }
    
    /**
     * @test
     */
    public function iCanHaveADynamicBundle()
    {
        $bundles = $this->getBundleService()->getActivatedBundles();
        $this->assertNotNull($bundles);
        $this->assertContains('Acme\SimpleBundle\AcmeSimpleBundle', $bundles);
    }

    /**
     * @test
     */
    public function iCanAddADynamicBundle()
    {
        $bundles = [
            "dynamic.bundles" => $this->getBundleService()->getActivatedBundles()
        ];

        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid(mt_rand());
        $tmpFile = tempnam(sys_get_temp_dir(), '');
        rename($tmpFile, $tmpFile .= '.yml');

        file_put_contents($tmpFile, $this->dumpAsYamlParameters($bundles));

        $kernel = $this->getTestKernel($cacheDir, $tmpFile);
        $bundleService = $kernel->getContainer()->get('dynamic_bundle_service');
        /* @var $bundleService \IneatConseil\DynamicBundle\Services\DynamicBundleService */
        
        $this->assertContains($this->acmeSimpleBundleFQDN, $bundleService->getActivatedBundles());
        $this->assertNotContains($this->acmeSimpleBundle2FQDN, $bundleService->getActivatedBundles());
        $kernel->shutdown();

        $bundles["dynamic.bundles"] = $this->acmeSimpleBundle2FQDN;
        file_put_contents($tmpFile, $this->dumpAsYamlParameters($bundles));
        $kernel = $this->getTestKernel($cacheDir, $tmpFile);
        $bundleService = $kernel->getContainer()->get('dynamic_bundle_service');
        $this->assertContains($this->acmeSimpleBundle2FQDN, $bundleService->getActivatedBundles());
        $kernel->shutdown();
    }

    protected function dumpAsYamlParameters($params)
    {
        return Yaml::dump(["parameters" => $params]);
    }

    protected function getTestKernel($cacheDir, $dynamicBundlesConfigurationFile)
    {
        $kernel = new AppKernelTest(uniqid(rand()), $cacheDir);
        $kernel->setDynamicBundlesConfigurationFile($dynamicBundlesConfigurationFile);
        $kernel->boot();
        return $kernel;
    }
}
