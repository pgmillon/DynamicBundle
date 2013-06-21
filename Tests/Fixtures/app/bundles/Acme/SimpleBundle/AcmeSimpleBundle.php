<?php

namespace Acme\SimpleBundle;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use IneatConseil\DynamicBundle\Bundle\DynamicBundle;

class AcmeSimpleBundle extends Bundle implements DynamicBundle
{

    public function getRouteCollection()
    {
        return new RouteCollection();
    }
}
