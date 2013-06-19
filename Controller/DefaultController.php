<?php

namespace IneatConseil\DynamicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use IneatConseil\DynamicBundle\Services\DynamicBundleService;

class DefaultController extends Controller
{
    /**
     *
     * @var DynamicBundleService 
     */
    protected $bundleService;

    public function getBundleService()
    {
        if (is_null($this->bundleService)) {
            $this->bundleService = $this->container->get('dynamic_bundle_service');
        }
        return $this->bundleService;
    }

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'activatedBundles' => $this->getBundleService()->getActivatedBundles(),
            'availableBundles' => $this->getBundleService()->getAvailableBundles()
        );
    }
}
