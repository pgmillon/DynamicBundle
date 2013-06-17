<?php

namespace IneatConseil\DynamicBundle\Config;

use Symfony\Component\Config\ConfigCache;

/**
 * Description of DynamicConfigCache
 *
 * @author Pierre-Gildas MILLON <pierre-gildas.millon@ineat-conseil.fr>
 */
class DynamicConfigCache extends ConfigCache
{

    public function isFresh()
    {
        if (parent::isFresh()) {
            
        }
        return false;
    }

    public function getMetadataFile()
    {
        return $this->file . '.meta';
    }

    public function getMetadata()
    {
        return unserialize(file_get_contents($this->getMetadataFile()));
    }
}
