<?php


namespace Authentication;


use Mobile_Detect;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    public function init(ModuleManager $manager)
    {
        $sharedeventManager = $manager->getEventManager()->getSharedManager();
        $sharedeventManager->attach(__NAMESPACE__, 'dispatch', [$this, 'onDispatch'], 10);
    }

    public function onDispatch(MvcEvent $event)
    {
        $controller = $event->getTarget();
        $controller->layout('layout/mobile');
    }
}