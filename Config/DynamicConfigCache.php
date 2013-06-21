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
    
    protected $kernel;
    
    public function __construct($kernel, $file, $debug)
    {
        parent::__construct($file, $debug);
        $this->kernel = $kernel;
        $this->file = $file;
    }
    
    public function isFresh()
    {
        if (parent::isFresh()) {
            $time = filemtime($this->getFile());
            foreach($this->getDynamicConfigurationFiles() as $configFile) {
                if($time <= filemtime($configFile)) {
                    return false;
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
    
    /**
     * 
     * @return \IneatConseil\DynamicBundle\HttpKernel\DynamicAppKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }
    
    public function getDynamicConfigurationFiles()
    {
        return array(
            $this->getKernel()->getDynamicBundlesConfigurationFile(),
        );
    }

}
