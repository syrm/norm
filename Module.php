<?php

namespace Norm;

use Zend\Mvc\MvcEvent;


class Module
{


    public function onBootstrap(MvcEvent $e)
    {

        $config = $e->getApplication()->getServiceManager()->get('config')['norm'];
        $configuration = \Norm\Configuration::getInstance();

        if (isset($config['model']) === true) {
            $configuration->setModel($config['model']);
        }

        if (isset($config['cache']) === true) {
            $configuration->setCache($config['cache']);
        }

        foreach($config['database'] as $name => $database) {
            $configuration->setConnection(
                $database['hostname'],
                $database['user'],
                $database['password'],
                $database['database'],
                $name);
        }

    }


    public function getAutoloaderConfig()
    {

        return array('Zend\Loader\StandardAutoloader' => array('namespaces' => array(__NAMESPACE__ => __DIR__)));

    }


}
