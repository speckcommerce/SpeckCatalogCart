<?php

namespace SpeckCatalogCart;

class Module
{
    protected $serviceManager;

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'speckCatalogCart' => 'SpeckCatalogCart\View\Helper\Cart',
            ),
        );
    }

    public function getConfig()
    {
        $config = array();
        $configFiles = array(
            __DIR__ . '/config/module.config.php',
        );
        foreach($configFiles as $configFile) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, include $configFile);
        }
        return $config;
    }

    public function onBootstrap($e)
    {
        //if($e->getRequest() instanceof \Zend\Console\Request){
        //    return;
        //}

        //$app = $e->getParam('application');

        //$sm  = $app->getServiceManager();
        //$this->setServiceManager($sm);

        //$em  = $app->getEventManager()->getSharedManager();
    }

    /**
     * @return serviceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param $serviceManager
     * @return self
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
