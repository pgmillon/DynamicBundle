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

    protected $file;
    
    protected $dynamicBundlesConfigurationFile;
    
    public function __construct($dynamicBundlesConfigurationFile, $file, $debug)
    {
        parent::__construct($file, $debug);
        $this->dynamicBundlesConfigurationFile = $dynamicBundlesConfigurationFile;
        $this->file = $file;
    }
    
    public function isFresh()
    {
        if (parent::isFresh()) {
            $time = filemtime($this->getFile());
            foreach ($this->getMetadata() as $resource) {
                /* @var $resource Symfony\Component\Config\Resource\ResourceInterface */
                if($this->dynamicBundlesConfigurationFile === $resource->getResource()) {
                    if (!$resource->isFresh($time)) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function getMetadataFile()
    {
        return $this->getFile() . '.meta';
    }

    public function getMetadata()
    {
        return unserialize(file_get_contents($this->getMetadataFile()));
    }
    
    public function getFile()
    {
        return $this->file;
    }
    
    public function getDynamicBundlesConfigurationFile()
    {
        return $this->dynamicBundlesConfigurationFile;
    }

}
