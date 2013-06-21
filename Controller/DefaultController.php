<?php

namespace IneatConseil\DynamicBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
            $kernel = $this->container->get('kernel');
            /* @var $kernel \IneatConseil\DynamicBundle\HttpKernel\DynamicAppKernel */

            $this->bundleService = $this->container->get($kernel->getDynamicBundlesServiceName());
        }
        return $this->bundleService;
    }

    /**
     * @Route("/", name="ineatconseil_dynamicbundle_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        return array(
            'form' => $this->getForm()->createView(),
        );
    }

    /**
     * @Route("/")
     * @Method("POST")
     * @Template
     */
    public function saveAction(Request $request)
    {
        $form = $this->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $this->getBundleService()->setActivatedBundles($data['bundles']);
        }

        return $this->redirect($this->generateUrl('ineatconseil_dynamicbundle_index'));
    }

    /**
     * 
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm()
    {
        $availableBundles = $this->getBundleService()->getAvailableBundles();
        $formBuilder = $this->createFormBuilder([
                'bundles' => $this->getBundleService()->getActivatedBundlesFQDN()
            ])
            ->add('bundles', 'choice', [
            'choices' => array_combine($availableBundles, $availableBundles),
            'multiple' => true,
            'required' => false,
        ]);
        return $formBuilder->getForm();
    }
}
