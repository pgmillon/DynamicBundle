<?php

namespace IneatConseil\DynamicBundle\Bundle;

use Symfony\Component\Routing\RouteCollection;

/**
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
interface DynamicBundle
{
    /**
     * 
     * @return RouteCollection
     */
    public function getRouteCollection();
}

