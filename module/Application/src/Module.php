<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

//use Application\Models\UtilsFile;
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

    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();

        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        $sessionManager = $serviceManager->get(SessionManager::class);
    }

    public function onDispatch(MvcEvent $event)
    {
        $mobileDetect = new Mobile_Detect();
        $controller = $event->getTarget();
        if ($mobileDetect->isMobile()) {
            $controller->layout('layout/mobile');
            if ($event->getRouteMatch()->getMatchedRouteName() !== 'application_mobile') {
                return $controller->redirect()->toRoute('application_mobile');
            }
        } else {
            $controller->layout('layout/browser');
            if ($event->getRouteMatch()->getMatchedRouteName() !== 'application_browser') {
                return $controller->redirect()->toRoute('application_browser');
            }
        }
        return $event;
    }
}
