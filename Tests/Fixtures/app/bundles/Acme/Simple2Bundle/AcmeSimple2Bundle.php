<?php

namespace Acme\Simple2Bundle;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use IneatConseil\DynamicBundle\Bundle\DynamicBundle;

class AcmeSimple2Bundle extends Bundle implements DynamicBundle
{

    public function getRouteCollection()
    {
        return new RouteCollection();
    }
}
