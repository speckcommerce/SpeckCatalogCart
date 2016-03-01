<?php

namespace SpeckCatalogCart;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Stdlib\ArrayUtils;

class Module implements
    ServiceProviderInterface,
    ControllerProviderInterface,
    ViewHelperProviderInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface
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
            $config = ArrayUtils::merge($config, include $configFile);
        }
        return $config;
    }

    public function getControllerConfig()
    {
        return include __DIR__ . '/config/service/controller.config.php';
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service/service.config.php';
    }
}
