<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

//use Application\Models\UtilsFile;
use Application\Debug\UtilsFile;
use Authentication\Service\AuthenticationManager;
use Mobile_Detect;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;

class Module
{
    const VERSION = '3.0.3-dev';
    /** @var $authManager AuthenticationManager */
    protected $authManager;

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
        $sessionManager = $serviceManager->get(SessionManager::class);
        $this->authManager = $serviceManager->get(AuthenticationManager::class);
    }

    public function onDispatch(MvcEvent $event)
    {
        $mobileDetect = new Mobile_Detect();
        $controller = $event->getTarget();
        if ($mobileDetect->isMobile()) {
            $controller->layout('layout/mobile');
            if ($this->authManager->userState()) {
                $userType = (int)$this->authManager->getActiveUser()['user_type'] === 1 ? 'user' : 'prof';
                if ($event->getRouteMatch()->getMatchedRouteName() !== "application_mobile_{$userType}") {
                    return $controller->redirect()->toRoute("application_mobile_{$userType}");
                }
            } else if ($event->getRouteMatch()->getMatchedRouteName() !== 'application_mobile_front') {
                return $controller->redirect()->toRoute('application_mobile_front');
            }
        } else {
            $controller->layout('layout/browser');
            if ($event->getRouteMatch()->getMatchedRouteName() !== 'application_browser') {
                return $controller->redirect()->toRoute('application_browser');
            }
        }
        return $event;
        //@TODO: Fazer com que erros 404 redirecionem para o front
    }
}
