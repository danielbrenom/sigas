<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\Factory\FrontAppControllerFactory;
use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\ProfessionalAppControllerFactory;
use Application\Controller\Factory\UserAppControllerFactory;
use Application\Controller\Mobile\FrontAppController;
use Application\Controller\Mobile\ProfessionalAppController;
use Application\Controller\Mobile\UserAppController;
use Application\Repository\Factory\MobileRepositoryFactory;
use Application\Repository\MobileRepository;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'home',
                    ],
                ],
            ],
            'application_browser' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/browser[/:action]',
                    'defaults' => [
                        'controller' => Controller\BrowserController::class,
                        'action' => 'home',
                    ],
                ],
            ],
            'application_mobile_user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/user[/:action]',
                    'defaults' => [
                        'controller' => Controller\Mobile\UserAppController::class,
                        'action' => 'home'
                    ]
                ]
            ],
            'application_mobile_prof' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/prof[/:action]',
                    'defaults' => [
                        'controller' => Controller\Mobile\ProfessionalAppController::class,
                        'action' => 'home'
                    ]
                ]
            ],
            'application_mobile_front' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile/front[/:action]',
                    'defaults' => [
                        'controller' => Controller\Mobile\FrontAppController::class,
                        'action' => 'index'
                    ]
                ]
            ],
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_entities' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_entities']
            ],
            'orm_crawler_chain' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_entities'
                ]
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            MobileRepository::class => MobileRepositoryFactory::class
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => IndexControllerFactory::class,
            Controller\BrowserController::class => InvokableFactory::class,
            UserAppController::class => UserAppControllerFactory::class,
            ProfessionalAppController::class => ProfessionalAppControllerFactory::class,
            FrontAppController::class => FrontAppControllerFactory::class
        ],
    ],
    'session_containers' => [
        'MessagesContainer'
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/mobile' => __DIR__ . '/../view/layout/layout_mobile.phtml',
            'layout/browser' => __DIR__ . '/../view/layout/layout_browser.phtml',
            'layout/layout' => __DIR__ . '/../view/layout/layout_browser.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
