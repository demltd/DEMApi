<?php
namespace DEMApi;

use DEMApi\Api;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'DEMApi\Api' => function($sm) {
                    $conf = $sm->get('Config');
                    
                    if(!isset($conf['demapi']['api_key']) || 
                        !isset($conf['demapi']['api_secret'])){
                        throw new \RuntimeException('DEMApi\Api requires api_key and api_secret '
                            . 'to be defined in your local configuration');
                    }
                    
                    return new Api($conf['demapi']['api_key'], $conf['demapi']['api_secret']);
                }
            ),
        );
    }
}