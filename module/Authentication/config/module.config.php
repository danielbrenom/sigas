<?php

namespace Authentication;

use Application\Controller\IndexController;
use Authentication\Controller\AuthenticationController;
use Authentication\Controller\Factory\AuthenticationControllerFactory;
use Authentication\Service\AuthenticationAdapter;
use Authentication\Service\AuthenticationManager;
use Authentication\Service\Factory\AuthenticationAdapterFactory;
use Authentication\Service\Factory\AuthenticationManagerFactory;
use Authentication\Service\Factory\UserManagerFactory;
use Authentication\Service\UserManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Zend\Authentication\AuthenticationService;
use Authentication\Service\Factory\AuthenticationServiceFactory;
use Zend\Router\Http\Literal;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' =>
        [
            'routes' =>
                [
                    'login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => Controller\AuthenticationController::class,
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => Controller\AuthenticationController::class,
                                'action' => 'logout',
                            ],
                        ],
                    ],
                    'user-state' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/user-state',
                            'defaults' => [
                                'controller' => Controller\AuthenticationController::class,
                                'action' => 'user-state',
                            ],
                        ],
                    ],
                    'singup' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/singup',
                            'defaults' => [
                                'controller' => Controller\AuthenticationController::class,
                                'action' => 'singup',
                            ],
                        ],
                    ],
                    'singup-prof' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/singup-prof',
                            'defaults' => [
                                'controller' => Controller\AuthenticationController::class,
                                'action' => 'singup-prof'
                            ]
                        ]
                    ]
                ]
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
    'controllers' => [
        'factories' => [
            AuthenticationController::class => AuthenticationControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => AuthenticationServiceFactory::class,
            AuthenticationAdapter::class => AuthenticationAdapterFactory::class,
            AuthenticationManager::class => AuthenticationManagerFactory::class,
            UserManager::class => UserManagerFactory::class
        ]
    ]
];