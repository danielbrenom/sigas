<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\MobileControllerFactory;
use Application\Controller\MobileController;
use Application\Controller\Plugin\Factory\HtmlRenderFactory;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

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
            'application_mobile' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/mobile[/:action]',
                    'defaults' => [
                        'controller' => Controller\MobileController::class,
                        'action' => 'home'
                    ]
                ]
            ]
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
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
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => IndexControllerFactory::class,
            Controller\BrowserController::class => InvokableFactory::class,
            MobileController::class => MobileControllerFactory::class
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'htmlRender' => HtmlRenderFactory::class
        ]
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
