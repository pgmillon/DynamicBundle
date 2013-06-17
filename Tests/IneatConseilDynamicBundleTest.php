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
    protected $acmeSimpleBundleFQDN = 'Acme\SimpleBundle\AcmeSimpleBundle';
    protected $acmeSimpleBundle2FQDN = 'Acme\SimpleBundle2\AcmeSimpleBundle2';

    protected function setUp()
    {
        parent::setUp();

        static::createClient();

        $this->setContainer(static::$kernel->getContainer());

        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
        $cacheClearer = $this->getContainer()->get('cache_clearer');
        /* @var $cacheClearer \Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface */

        $cacheClearer->clear($cacheDir);
    }

    /**
     * 
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return $this->container;
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
     * @test
     */
    public function iCanHaveADynamicBundle()
    {
        $bundles = $this->getContainer()->getParameter('dynamic.bundles');
        $this->assertNotNull($bundles);
        $this->assertContains('Acme\SimpleBundle\AcmeSimpleBundle', $bundles);
    }

    /**
     * @test
     */
    public function iCanAddADynamicBundle()
    {
        $bundles = [
            "dynamic.bundles" => $this->getContainer()->getParameter('dynamic.bundles')
        ];

        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid(mt_rand());
        $tmpFile = tempnam(sys_get_temp_dir(), '');
        rename($tmpFile, $tmpFile .= '.yml');

        file_put_contents($tmpFile, $this->dumpAsYamlParameters($bundles));

        $kernel = $this->getTestKernel($cacheDir, $tmpFile);
        $container = $kernel->getContainer();
        $this->assertContains($this->acmeSimpleBundleFQDN, $container->getParameter('dynamic.bundles'));
        $this->assertNotContains($this->acmeSimpleBundle2FQDN, $container->getParameter('dynamic.bundles'));
        $kernel->shutdown();

        $bundles["dynamic.bundles"] = $this->acmeSimpleBundle2FQDN;
        file_put_contents($tmpFile, $this->dumpAsYamlParameters($bundles));
        $kernel = $this->getTestKernel($cacheDir, $tmpFile);
        $container = $kernel->getContainer();
        $this->assertContains($this->acmeSimpleBundle2FQDN, $container->getParameter('dynamic.bundles'));
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
