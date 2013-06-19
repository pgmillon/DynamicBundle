<?php

namespace {
    
    use IneatConseil\DynamicBundle\HttpKernel\DynamicAppKernel;
    use Symfony\Component\Config\Loader\LoaderInterface;
    
    class AppKernel extends DynamicAppKernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
                new Symfony\Bundle\SecurityBundle\SecurityBundle(),
                new Symfony\Bundle\TwigBundle\TwigBundle(),
                new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
                new Sensio\Bundle\DistributionBundle\SensioDistributionBundle(),
                new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            );

            return $bundles;
        }

        public function registerContainerConfiguration(LoaderInterface $loader)
        {
            parent::registerContainerConfiguration($loader);
            $loader->load(__DIR__.'/config/config.yml');
        }
    }
}
